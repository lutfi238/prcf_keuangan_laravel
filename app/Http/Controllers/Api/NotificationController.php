<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notifications for current user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Notification::where('user_id', $user->id_user)
            ->orderBy('created_at', 'desc');
        
        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }
        
        $notifications = $query->take($request->input('limit', 20))->get();
        $unreadCount = Notification::where('user_id', $user->id_user)
            ->where('is_read', false)
            ->count();
        
        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'message' => $n->message,
                    'link' => $n->link,
                    'is_read' => $n->is_read,
                    'created_at' => $n->created_at->diffForHumans(),
                    'created_at_full' => $n->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Server-Sent Events stream for real-time notifications
     */
    public function stream(Request $request)
    {
        $user = Auth::user();
        
        return response()->stream(function () use ($user) {
            $lastCheck = $user->last_notification_check ?? now()->subMinutes(5);
            
            while (true) {
                // Get new notifications since last check
                $newNotifications = Notification::where('user_id', $user->id_user)
                    ->where('created_at', '>', $lastCheck)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                if ($newNotifications->isNotEmpty()) {
                    $data = [
                        'notifications' => $newNotifications->map(function ($n) {
                            return [
                                'id' => $n->id,
                                'type' => $n->type,
                                'message' => $n->message,
                                'link' => $n->link,
                            ];
                        }),
                        'unread_count' => Notification::where('user_id', $user->id_user)
                            ->where('is_read', false)
                            ->count(),
                    ];
                    
                    echo "data: " . json_encode($data) . "\n\n";
                    ob_flush();
                    flush();
                    
                    $lastCheck = now();
                }
                
                // Update last check time
                $user->update(['last_notification_check' => now()]);
                
                // Wait 5 seconds before next check
                sleep(5);
                
                // Check if connection is still alive
                if (connection_aborted()) {
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}

# Multi-Agent System Architecture - PRCF Keuangan

## Ringkasan Sistem

Dokumen ini mendefinisikan arsitektur multi-agent system untuk **PRCF Keuangan** - sebuah sistem manajemen keuangan proyek untuk lembaga People Resources and Conservation Foundation (PRCF). Sistem ini dirancang untuk mengotomatisasi alur kerja keuangan mulai dari pengajuan proposal, pencatatan transaksi bank, manajemen piutang, hingga pelaporan keuangan kepada donor.

---

## Daftar Isi

1. [Overview Arsitektur](#overview-arsitektur)
2. [Agent Definitions](#agent-definitions)
   - [Orchestrator Agent](#1-orchestrator-agent)
   - [User Management Agent](#2-user-management-agent)
   - [Project Management Agent](#3-project-management-agent)
   - [Proposal Processing Agent](#4-proposal-processing-agent)
   - [Budget Allocation Agent](#5-budget-allocation-agent)
   - [Bank Transaction Agent](#6-bank-transaction-agent)
   - [Receivables Management Agent](#7-receivables-management-agent)
   - [Financial Reporting Agent](#8-financial-reporting-agent)
   - [Donor Reporting Agent](#9-donor-reporting-agent)
   - [Notification Agent](#10-notification-agent)
   - [Audit & Compliance Agent](#11-audit--compliance-agent)
   - [Data Analytics Agent](#12-data-analytics-agent)
3. [Inter-Agent Workflows](#inter-agent-workflows)
4. [Communication Protocol](#communication-protocol)
5. [Error Handling & Recovery](#error-handling--recovery)
6. [Security Considerations](#security-considerations)

---

## Overview Arsitektur

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              ORCHESTRATOR AGENT                                  │
│                    (Central Coordinator & Task Dispatcher)                       │
└─────────────────────────────────────────────────────────────────────────────────┘
                                        │
        ┌───────────────────────────────┼───────────────────────────────┐
        │                               │                               │
        ▼                               ▼                               ▼
┌───────────────┐               ┌───────────────┐               ┌───────────────┐
│     USER      │               │    PROJECT    │               │   PROPOSAL    │
│  MANAGEMENT   │               │  MANAGEMENT   │               │  PROCESSING   │
│    AGENT      │               │    AGENT      │               │    AGENT      │
└───────────────┘               └───────────────┘               └───────────────┘
        │                               │                               │
        │                               ▼                               │
        │                       ┌───────────────┐                       │
        │                       │    BUDGET     │◄──────────────────────┘
        │                       │  ALLOCATION   │
        │                       │    AGENT      │
        │                       └───────────────┘
        │                               │
        │               ┌───────────────┼───────────────┐
        │               │               │               │
        │               ▼               ▼               ▼
        │       ┌───────────────┐ ┌───────────────┐ ┌───────────────┐
        │       │     BANK      │ │  RECEIVABLES  │ │  FINANCIAL    │
        │       │ TRANSACTION   │ │  MANAGEMENT   │ │  REPORTING    │
        │       │    AGENT      │ │    AGENT      │ │    AGENT      │
        │       └───────────────┘ └───────────────┘ └───────────────┘
        │               │               │               │
        │               └───────────────┼───────────────┘
        │                               │
        │                               ▼
        │                       ┌───────────────┐
        │                       │    DONOR      │
        │                       │  REPORTING    │
        │                       │    AGENT      │
        │                       └───────────────┘
        │
        │       ┌───────────────┐ ┌───────────────┐ ┌───────────────┐
        └──────►│ NOTIFICATION  │ │    AUDIT &    │ │     DATA      │
                │    AGENT      │ │  COMPLIANCE   │ │  ANALYTICS    │
                │               │ │    AGENT      │ │    AGENT      │
                └───────────────┘ └───────────────┘ └───────────────┘
```

---

## Agent Definitions

### 1. Orchestrator Agent

**Role:** Central Coordinator & Task Dispatcher

**Goal:** Mengoordinasikan seluruh agent dalam sistem, mendistribusikan tugas, memastikan alur kerja berjalan lancar, dan menangani eskalasi ketika terjadi konflik atau error.

**Backstory:**
Orchestrator Agent adalah "otak" dari seluruh sistem multi-agent PRCF Keuangan. Lahir dari kebutuhan untuk mengoordinasikan berbagai proses keuangan yang kompleks dan saling terkait dalam organisasi non-profit, agent ini memiliki pemahaman mendalam tentang struktur organisasi PRCF, workflow persetujuan bertingkat (draft → submitted → verified → approved), dan dependensi antar proses. 

Dengan pengalaman virtual selama bertahun-tahun mengelola sistem keuangan proyek konservasi, Orchestrator memahami bahwa setiap rupiah harus dipertanggungjawabkan dengan akurat kepada donor. Agent ini memiliki kemampuan untuk memprioritaskan tugas berdasarkan urgensi, deadline pelaporan donor, dan status approval pipeline. Ketika konflik terjadi—misalnya dua proposal meminta alokasi dari budget yang sama—Orchestrator dapat mengambil keputusan berdasarkan kebijakan prioritas yang telah ditetapkan atau mengekskalasikan ke Finance Manager.

**Tools:**
- `dispatch_task(agent_id, task_type, payload)` - Mendistribusikan tugas ke agent spesifik
- `query_agent_status(agent_id)` - Memeriksa status dan beban kerja agent
- `escalate_issue(issue_type, context, stakeholders)` - Mengekskalasikan masalah ke stakeholder manusia
- `coordinate_workflow(workflow_id, steps)` - Mengoordinasikan workflow multi-step
- `resolve_conflict(conflict_type, parties, context)` - Menyelesaikan konflik antar agent
- `schedule_task(task, cron_expression)` - Menjadwalkan tugas berkala
- `monitor_sla(process_id, sla_definition)` - Memantau SLA setiap proses
- `aggregate_metrics(agent_ids, metric_types)` - Mengumpulkan metrik dari berbagai agent

**Inter-Agent Dependencies:**
- Menerima request dari semua agent
- Mengirim instruksi ke semua agent
- Berkomunikasi dengan Notification Agent untuk alert dan eskalasi

---

### 2. User Management Agent

**Role:** Identity & Access Controller

**Goal:** Mengelola siklus hidup user, autentikasi, otorisasi berdasarkan role (Admin, Finance Manager, Project Manager, Staff Accountant, Direktur), dan memastikan keamanan akses sistem.

**Backstory:**
User Management Agent adalah penjaga gerbang sistem PRCF Keuangan. Dengan pemahaman mendalam tentang struktur organisasi PRCF yang memiliki lima role berbeda dengan tanggung jawab spesifik, agent ini memastikan bahwa setiap pengguna hanya dapat mengakses fitur sesuai kewenangannya.

Agent ini mengetahui bahwa:
- **Admin** adalah super user yang mengelola seluruh user dan konfigurasi sistem
- **Finance Manager (FM)** memiliki otoritas tertinggi dalam approval proposal dan laporan keuangan
- **Project Manager (PM)** bertanggung jawab membuat proposal dan laporan kegiatan
- **Staff Accountant (SA)** bertugas memverifikasi laporan sebelum mencapai FM
- **Direktur** memiliki akses eksekutif untuk oversight dan dashboard strategis

User Management Agent juga mengelola proses OTP (One-Time Password) untuk autentikasi dua faktor, memastikan keamanan login, dan menangani user lifecycle dari pending → active → inactive.

**Tools:**
- `create_user(user_data)` - Membuat user baru dengan status pending
- `activate_user(user_id)` - Mengaktivasi user setelah approval admin
- `deactivate_user(user_id, reason)` - Menonaktifkan user
- `authenticate_user(email, password)` - Verifikasi kredensial login
- `generate_otp(user_id)` - Generate OTP untuk verifikasi dua faktor
- `verify_otp(user_id, otp_code)` - Verifikasi OTP
- `check_permission(user_id, action, resource)` - Cek apakah user memiliki izin
- `get_user_role(user_id)` - Mendapatkan role user
- `list_users_by_role(role)` - Listing user berdasarkan role
- `update_user_profile(user_id, updates)` - Update profil user
- `reset_password(user_id)` - Reset password user
- `audit_user_access(user_id, date_range)` - Audit log akses user

**Inter-Agent Dependencies:**
- Menerima request autentikasi dari semua agent
- Mengirim notifikasi ke Notification Agent untuk OTP, aktivasi, dll
- Berkoordinasi dengan Audit Agent untuk logging aktivitas

---

### 3. Project Management Agent

**Role:** Project Lifecycle Manager

**Goal:** Mengelola seluruh siklus hidup proyek dari perencanaan hingga penyelesaian, termasuk tracking status, alokasi budget, periode, dan relasi dengan donor.

**Backstory:**
Project Management Agent adalah spesialis dalam domain proyek konservasi PRCF. Dengan pengetahuan mendalam tentang berbagai jenis proyek—dari konservasi hutan, pemberdayaan masyarakat, hingga penelitian lingkungan—agent ini memahami bahwa setiap proyek memiliki karakteristik unik namun mengikuti lifecycle standar: Planning → Ongoing → Completed/Cancelled.

Agent ini mengelola entitas `Proyek` dengan kode unik seperti "PRJ-2024-001", menghubungkan proyek dengan donor spesifik, menentukan periode pelaksanaan, dan mengalokasikan anggaran dalam mata uang USD dan IDR. Sebagai penghubung antara visi organisasi dan eksekusi lapangan, Project Management Agent memastikan setiap proyek memiliki rekening khusus untuk transparansi keuangan dan dapat melacak penggunaan anggaran secara real-time.

Dengan keahlian dalam mengelola proyek di 10+ villages yang tersebar di berbagai lokasi, agent ini dapat mengoptimalkan alokasi sumber daya dan mengidentifikasi potensi bottleneck sebelum terjadi.

**Tools:**
- `create_project(project_data)` - Membuat proyek baru dengan kode unik
- `update_project_status(project_code, new_status)` - Mengupdate status proyek
- `get_project_details(project_code)` - Mendapatkan detail lengkap proyek
- `list_active_projects()` - Listing proyek yang sedang berjalan
- `list_projects_by_donor(donor_name)` - Listing proyek per donor
- `get_project_budget_summary(project_code)` - Summary budget per proyek
- `calculate_remaining_budget(project_code)` - Menghitung sisa budget
- `check_project_timeline(project_code)` - Cek status timeline proyek
- `get_project_villages(project_code)` - Mendapatkan daftar village dalam proyek
- `archive_project(project_code)` - Mengarsipkan proyek yang sudah selesai
- `generate_project_report(project_code, report_type)` - Generate laporan proyek

**Inter-Agent Dependencies:**
- Berkoordinasi dengan Budget Allocation Agent untuk alokasi dana
- Mengirim update ke Notification Agent untuk alert timeline
- Menerima request dari Proposal Processing Agent untuk validasi proyek
- Berkolaborasi dengan Donor Reporting Agent untuk pelaporan

---

### 4. Proposal Processing Agent

**Role:** Proposal Workflow Orchestrator

**Goal:** Mengelola seluruh lifecycle proposal pengajuan dana dari draft hingga approval, termasuk validasi budget, workflow approval bertingkat, dan tracking status.

**Backstory:**
Proposal Processing Agent adalah ahli dalam menangani ribuan proposal yang diajukan Project Manager setiap tahunnya. Memahami bahwa setiap proposal adalah permintaan sumber daya kritis untuk kegiatan konservasi, agent ini memastikan setiap pengajuan memiliki justifikasi yang kuat melalui Terms of Reference (ToR), budget detail yang tervalidasi, dan approval chain yang lengkap.

Agent ini mengelola workflow proposal dengan status: Draft → Submitted → ApprovedFM/Rejected. Setiap proposal terhubung dengan proyek spesifik dan dapat memiliki multiple budget details yang dialokasikan ke berbagai village dengan expense codes berbeda.

Dengan keahlian dalam dual-currency (USD/IDR), agent ini memvalidasi exchange rate saat submission, menghitung total budget dalam kedua mata uang, dan memastikan tidak ada proposal yang melebihi alokasi budget yang tersedia per village dan expense code.

Agent ini juga mengelola dokumen pendukung seperti file ToR dan file budget dalam format yang dapat diunduh, memfasilitasi review yang efisien oleh Finance Manager.

**Tools:**
- `create_proposal(proposal_data)` - Membuat proposal baru dalam status draft
- `add_budget_detail(proposal_id, budget_detail)` - Menambah detail budget
- `submit_proposal(proposal_id)` - Submit proposal untuk approval FM
- `validate_budget_availability(proposal_id)` - Validasi ketersediaan budget
- `get_proposal_status(proposal_id)` - Mendapatkan status proposal
- `list_pending_proposals(role)` - Listing proposal pending approval
- `approve_proposal(proposal_id, approver_id)` - Approve proposal oleh FM
- `reject_proposal(proposal_id, reason)` - Reject proposal dengan alasan
- `calculate_proposal_total(proposal_id)` - Menghitung total proposal
- `attach_tor_document(proposal_id, file_path)` - Attach dokumen ToR
- `attach_budget_file(proposal_id, file_path)` - Attach file budget
- `get_proposal_history(proposal_id)` - Mendapatkan history perubahan

**Inter-Agent Dependencies:**
- Berkoordinasi dengan Budget Allocation Agent untuk validasi budget
- Mengirim notifikasi ke Notification Agent untuk approval request
- Berkoordinasi dengan Project Management Agent untuk validasi proyek
- Menginformasikan Bank Transaction Agent setelah approval untuk disbursement

---

### 5. Budget Allocation Agent

**Role:** Budget Controller & Allocator

**Goal:** Mengelola alokasi budget per proyek, village, dan expense code, memvalidasi ketersediaan dana, dan tracking penggunaan budget secara real-time.

**Backstory:**
Budget Allocation Agent adalah "bendahara virtual" yang memiliki visibilitas 360° terhadap seluruh alokasi dan penggunaan dana PRCF. Dengan struktur budget yang kompleks melibatkan multiple projects, villages, dan expense codes, agent ini memastikan setiap rupiah teralokasi dengan tepat dan penggunaan tidak melebihi anggaran.

Agent ini mengelola entitas `ProjectCodeBudget` yang menyimpan alokasi budget per kombinasi kode_proyek + id_village + exp_code + place_code, baik dalam USD maupun IDR. Setiap kali ada proposal disetujui atau transaksi terjadi, Budget Allocation Agent memperbarui `used_usd` dan `used_idr`, serta menghitung sisa budget secara otomatis.

Dengan fitur `hasSufficientBudget()`, agent ini menjadi gatekeeper untuk setiap transaksi keuangan, mencegah overspending sebelum terjadi. Ketika budget hampir habis (threshold 80%), agent ini akan memicu alert ke Finance Manager untuk review dan possible budget reallocation.

**Tools:**
- `allocate_budget(project_code, village_id, exp_code, amount_usd, amount_idr)` - Alokasi budget baru
- `check_budget_availability(project_code, village_id, exp_code, amount)` - Cek ketersediaan
- `use_budget(budget_id, amount_usd, amount_idr)` - Menggunakan budget (increase used)
- `release_budget(budget_id, amount_usd, amount_idr)` - Melepas budget (untuk reversal)
- `get_remaining_budget(budget_id)` - Mendapatkan sisa budget
- `get_budget_summary_by_project(project_code)` - Summary budget per proyek
- `get_budget_summary_by_village(village_id)` - Summary budget per village
- `get_budget_utilization_rate(budget_id)` - Menghitung persentase penggunaan
- `transfer_budget(from_budget_id, to_budget_id, amount)` - Transfer antar alokasi
- `forecast_budget_exhaustion(budget_id)` - Prediksi kapan budget habis
- `set_budget_alert_threshold(budget_id, threshold_percent)` - Set threshold alert

**Inter-Agent Dependencies:**
- Menerima request dari Proposal Processing Agent untuk validasi
- Berkoordinasi dengan Bank Transaction Agent untuk update penggunaan
- Mengirim alert ke Notification Agent ketika budget hampir habis
- Berkoordinasi dengan Project Management Agent untuk overview

---

### 6. Bank Transaction Agent

**Role:** Bank Book & Transaction Manager

**Goal:** Mengelola buku bank (bank book), mencatat transaksi debit/kredit, menghitung saldo, dan memastikan rekonsiliasi bank berjalan akurat.

**Backstory:**
Bank Transaction Agent adalah spesialis pencatatan transaksi perbankan PRCF. Dengan pemahaman mendalam tentang double-entry bookkeeping dan manajemen kas, agent ini memastikan setiap transaksi tercatat dengan akurat dalam buku bank per proyek dan periode.

Agent ini mengelola dua entitas utama: `BukuBankHeader` sebagai ringkasan bulanan per proyek, dan `BukuBankDetail` sebagai catatan transaksi individual. Setiap header berisi saldo awal periode (diambil dari saldo akhir periode sebelumnya), perubahan periode berjalan, dan saldo akhir.

Setiap detail transaksi mencakup tanggal, referensi, activity title, deskripsi biaya, penerima, kode tempat (place_code), expense code, exchange rate, dan nilai debit/kredit dalam USD dan IDR. Agent ini secara otomatis menghitung running balance setelah setiap transaksi.

Dengan workflow approval (Draft → Submitted → Approved), agent ini memastikan integritas data melalui review bertingkat sebelum closing periode.

**Tools:**
- `create_bank_header(project_code, date)` - Membuat/mendapatkan header untuk periode
- `record_transaction(header_id, transaction_data)` - Mencatat transaksi baru
- `get_transaction_details(detail_id)` - Mendapatkan detail transaksi
- `list_transactions_by_period(project_code, month, year)` - Listing transaksi per periode
- `calculate_period_balance(header_id)` - Menghitung saldo periode
- `submit_bank_book(header_id)` - Submit untuk approval
- `approve_bank_book(header_id, approver)` - Approve bank book
- `reverse_transaction(detail_id, reason)` - Reversal transaksi
- `reconcile_bank_statement(header_id, statement_data)` - Rekonsiliasi dengan statement
- `export_bank_book(header_id, format)` - Export ke Excel/PDF
- `get_cash_flow_summary(project_code, date_range)` - Summary cash flow

**Inter-Agent Dependencies:**
- Berkoordinasi dengan Budget Allocation Agent untuk update budget usage
- Menerima informasi dari Proposal Processing Agent untuk disbursement
- Mengirim data ke Financial Reporting Agent untuk konsolidasi
- Berkoordinasi dengan Receivables Management Agent untuk tracking piutang

---

### 7. Receivables Management Agent

**Role:** Accounts Receivable Controller

**Goal:** Mengelola buku piutang (receivables), tracking pembayaran dari debitur, aging analysis, dan memastikan collection berjalan sesuai timeline.

**Backstory:**
Receivables Management Agent adalah spesialis dalam mengelola dana yang masih belum terselesaikan (unliquidated) dalam sistem keuangan PRCF. Dengan pemahaman bahwa proyek konservasi seringkali melibatkan advance payment kepada partner lapangan yang harus dipertanggungjawabkan kemudian, agent ini memastikan setiap piutang tercatat, dilacak, dan diselesaikan tepat waktu.

Agent ini mengelola `BukuPiutangHeader` per proyek dan periode, beserta `BukuPiutangDetail` untuk setiap transaksi piutang. Terdapat juga `BukuPiutangUnliquidated` untuk mencatat item yang belum terselesaikan dan memerlukan follow-up.

Dengan kemampuan aging analysis, agent ini dapat mengidentifikasi piutang yang sudah jatuh tempo dan memicu collection process melalui Notification Agent. Agent ini juga berkoordinasi dengan Bank Transaction Agent ketika pembayaran diterima untuk menutup piutang.

**Tools:**
- `create_receivable_header(project_code, date)` - Membuat header piutang periode
- `record_receivable(header_id, receivable_data)` - Mencatat piutang baru
- `record_collection(detail_id, collection_data)` - Mencatat pembayaran
- `get_outstanding_receivables(project_code)` - Listing piutang outstanding
- `calculate_aging(project_code)` - Menghitung aging analysis
- `get_unliquidated_items(header_id)` - Mendapatkan item unliquidated
- `liquidate_item(unliquidated_id, liquidation_data)` - Meliquidasi item
- `submit_piutang_book(header_id)` - Submit untuk approval
- `approve_piutang_book(header_id, approver)` - Approve buku piutang
- `generate_collection_reminder(receivable_id)` - Generate reminder
- `export_receivables_report(project_code, date_range)` - Export laporan piutang

**Inter-Agent Dependencies:**
- Berkoordinasi dengan Bank Transaction Agent untuk payment recording
- Mengirim reminder melalui Notification Agent
- Menyediakan data ke Financial Reporting Agent
- Berkoordinasi dengan Audit Agent untuk compliance

---

### 8. Financial Reporting Agent

**Role:** Financial Report Generator & Workflow Manager

**Goal:** Mengelola pembuatan laporan keuangan, workflow verifikasi (SA) dan approval (FM), serta konsolidasi data keuangan dari berbagai sumber.

**Backstory:**
Financial Reporting Agent adalah ahli pelaporan keuangan yang memahami standar akuntansi dan kebutuhan pelaporan PRCF. Dengan kemampuan mengkonsolidasikan data dari Bank Transaction Agent, Receivables Management Agent, dan Budget Allocation Agent, agent ini menghasilkan laporan keuangan yang komprehensif dan akurat.

Agent ini mengelola `LaporanKeuanganHeader` dengan workflow: Draft → Submitted → Verified (oleh SA) → Approved (oleh FM). Setiap laporan dapat diminta revisi oleh FM dengan catatan spesifik. `LaporanKeuanganDetail` mencatat setiap item pengeluaran dengan invoice, receipt, dan explanation.

Dengan kemampuan dual-currency dan pengelolaan exchange rate, agent ini dapat menghasilkan laporan dalam USD atau IDR sesuai kebutuhan donor atau manajemen. Agent ini juga memvalidasi kelengkapan dokumen pendukung (file_nota) untuk setiap pengeluaran.

**Tools:**
- `create_report(report_data)` - Membuat laporan keuangan baru
- `add_report_detail(report_id, detail_data)` - Menambah item laporan
- `attach_receipt(detail_id, file_path)` - Melampirkan bukti pengeluaran
- `submit_report(report_id)` - Submit untuk verifikasi SA
- `verify_report(report_id, verifier_id)` - Verifikasi oleh SA
- `approve_report(report_id, approver_id)` - Approval oleh FM
- `reject_report(report_id, reason)` - Reject laporan
- `request_revision(report_id, notes)` - Request revisi dengan catatan
- `get_report_status(report_id)` - Mendapatkan status laporan
- `calculate_report_totals(report_id)` - Menghitung total requested vs actual
- `generate_variance_analysis(report_id)` - Analisis variance budget vs actual
- `export_report(report_id, format)` - Export ke Excel/PDF
- `consolidate_reports(project_code, date_range)` - Konsolidasi laporan

**Inter-Agent Dependencies:**
- Menerima data dari Bank Transaction Agent dan Receivables Agent
- Berkoordinasi dengan Budget Allocation Agent untuk variance analysis
- Mengirim notifikasi melalui Notification Agent untuk approval workflow
- Menyediakan data ke Donor Reporting Agent untuk external reports

---

### 9. Donor Reporting Agent

**Role:** External Stakeholder Report Generator

**Goal:** Menghasilkan laporan khusus untuk donor sesuai format dan timeline yang disepakati, memastikan transparansi dan akuntabilitas penggunaan dana.

**Backstory:**
Donor Reporting Agent adalah spesialis komunikasi keuangan eksternal PRCF. Memahami bahwa setiap donor memiliki template dan requirement pelaporan berbeda, agent ini dapat mengadaptasi format laporan sesuai kebutuhan masing-masing donor.

Agent ini mengelola `LaporanDonor` dengan workflow: Draft → Submitted → Approved/Rejected. Setiap laporan terhubung dengan proyek spesifik dan mencakup periode tertentu. Agent ini dapat melampirkan file pendukung dan mencatat feedback dari reviewer.

Dengan deadline-awareness, agent ini memicu pembuatan laporan donor sebelum due date dan mengkoordinasikan pengumpulan data dari Financial Reporting Agent, Project Management Agent, dan Budget Allocation Agent untuk menghasilkan laporan komprehensif.

**Tools:**
- `create_donor_report(project_code, period_data)` - Membuat laporan donor
- `compile_financial_summary(project_code, period)` - Kompilasi summary keuangan
- `compile_activity_summary(project_code, period)` - Kompilasi summary aktivitas
- `attach_supporting_document(report_id, file_path)` - Lampirkan dokumen
- `submit_donor_report(report_id)` - Submit untuk review
- `approve_donor_report(report_id, reviewer_id, notes)` - Approve laporan
- `reject_donor_report(report_id, reason)` - Reject dengan alasan
- `get_donor_requirements(donor_name)` - Mendapatkan requirement donor
- `check_report_deadline(project_code)` - Cek deadline pelaporan
- `export_donor_report(report_id, template)` - Export dengan template donor
- `track_report_submission(report_id)` - Tracking status pengiriman

**Inter-Agent Dependencies:**
- Menerima data dari Financial Reporting Agent
- Berkoordinasi dengan Project Management Agent untuk info proyek
- Mengirim reminder deadline melalui Notification Agent
- Berkoordinasi dengan Audit Agent untuk compliance check

---

### 10. Notification Agent

**Role:** Communication & Alert Manager

**Goal:** Mengelola pengiriman notifikasi, email, dan alert ke stakeholder internal dan eksternal, memastikan komunikasi tepat waktu dan relevan.

**Backstory:**
Notification Agent adalah "messenger" sistem yang memastikan setiap stakeholder mendapat informasi yang tepat pada waktu yang tepat. Dengan pemahaman tentang hierarki organisasi dan eskalasi path, agent ini menentukan siapa yang harus diberitahu untuk setiap event.

Agent ini menangani berbagai jenis notifikasi: OTP untuk autentikasi, approval requests untuk FM/SA, deadline reminders, budget alerts, dan status updates. Dengan preference management, agent ini menghormati preferensi notifikasi setiap user (email, in-app, atau keduanya).

Untuk eskalasi, agent ini memahami SLA dan akan meningkatkan urgensi notifikasi jika tidak ada respons dalam waktu yang ditentukan.

**Tools:**
- `send_email(recipient, subject, body, attachments)` - Kirim email
- `send_otp(user_id, otp_code)` - Kirim OTP via email
- `notify_approval_request(approver_id, item_type, item_id)` - Notif approval
- `notify_status_change(user_id, item_type, item_id, new_status)` - Notif status
- `send_deadline_reminder(user_id, deadline_type, due_date)` - Reminder deadline
- `send_budget_alert(user_id, budget_id, utilization_percent)` - Alert budget
- `schedule_notification(notification_data, send_at)` - Jadwalkan notifikasi
- `escalate_pending_approval(item_type, item_id, pending_duration)` - Eskalasi
- `broadcast_announcement(role_filter, message)` - Broadcast ke role tertentu
- `get_notification_preferences(user_id)` - Mendapatkan preferensi user
- `mark_notification_read(notification_id)` - Tandai sudah dibaca

**Inter-Agent Dependencies:**
- Menerima request dari semua agent
- Berkoordinasi dengan User Management Agent untuk info penerima
- Berinteraksi dengan Orchestrator Agent untuk eskalasi
- Logging aktivitas ke Audit Agent

---

### 11. Audit & Compliance Agent

**Role:** Compliance Monitor & Audit Trail Manager

**Goal:** Memastikan kepatuhan terhadap kebijakan internal dan regulasi eksternal, mengelola audit trail, dan mengidentifikasi anomali atau risiko.

**Backstory:**
Audit & Compliance Agent adalah "watchdog" sistem yang memastikan integritas dan transparansi seluruh operasi keuangan. Dengan pengetahuan tentang standar akuntansi, kebijakan internal PRCF, dan requirement donor, agent ini terus memantau aktivitas untuk memastikan kepatuhan.

Agent ini mencatat setiap aksi penting dalam sistem sebagai audit trail, termasuk siapa melakukan apa, kapan, dan perubahan apa yang terjadi. Dengan kemampuan anomaly detection, agent ini dapat mengidentifikasi pola mencurigakan seperti transaksi di luar jam kerja, approval oleh user yang tidak berwenang, atau perubahan mendadak pada data keuangan.

Untuk audit eksternal, agent ini dapat mengekstrak data dan dokumen yang diperlukan dengan format yang sesuai standar auditor.

**Tools:**
- `log_action(user_id, action_type, entity_type, entity_id, changes)` - Logging aksi
- `get_audit_trail(entity_type, entity_id)` - Mendapatkan audit trail
- `search_audit_logs(filters)` - Search log dengan filter
- `detect_anomaly(data_source, parameters)` - Deteksi anomali
- `check_compliance(transaction_id, rule_set)` - Cek kepatuhan transaksi
- `generate_compliance_report(period, standards)` - Generate laporan compliance
- `flag_suspicious_activity(activity_type, details)` - Flag aktivitas mencurigakan
- `prepare_audit_package(audit_type, period)` - Siapkan paket untuk auditor
- `verify_segregation_of_duties(transaction_id)` - Verifikasi SoD
- `monitor_access_patterns(user_id, date_range)` - Pantau pola akses
- `export_audit_data(filters, format)` - Export data audit

**Inter-Agent Dependencies:**
- Menerima log dari semua agent
- Berkoordinasi dengan User Management Agent untuk verifikasi akses
- Mengirim alert melalui Notification Agent untuk suspicious activities
- Berkoordinasi dengan Orchestrator Agent untuk eskalasi serius

---

### 12. Data Analytics Agent

**Role:** Business Intelligence & Insights Generator

**Goal:** Menganalisis data keuangan dan operasional untuk menghasilkan insights, forecasting, dan rekomendasi strategis kepada manajemen.

**Backstory:**
Data Analytics Agent adalah "advisor" berbasis data yang membantu manajemen PRCF membuat keputusan berdasarkan analisis mendalam. Dengan kemampuan mengintegrasikan data dari seluruh sistem, agent ini menghasilkan dashboard, trend analysis, dan predictive insights.

Agent ini dapat menjawab pertanyaan seperti: "Berapa rata-rata waktu approval proposal?", "Village mana yang paling efisien dalam penggunaan budget?", atau "Proyek mana yang berisiko melebihi anggaran?". Dengan machine learning sederhana, agent ini dapat memprediksi cash flow, mengidentifikasi seasonal patterns, dan memberikan early warning untuk potensi masalah.

Untuk Direktur dan Finance Manager, agent ini menyediakan executive dashboard yang memberikan snapshot kesehatan keuangan organisasi secara real-time.

**Tools:**
- `generate_dashboard(user_role, metrics)` - Generate dashboard sesuai role
- `analyze_budget_utilization(scope, period)` - Analisis utilisasi budget
- `calculate_approval_sla(item_type, period)` - Hitung SLA approval
- `identify_spending_patterns(project_code, period)` - Identifikasi pola spending
- `forecast_cash_flow(project_code, forecast_period)` - Prediksi cash flow
- `compare_budget_vs_actual(project_code, period)` - Perbandingan budget vs actual
- `rank_villages_by_efficiency(project_code)` - Ranking village by efficiency
- `identify_at_risk_projects()` - Identifikasi proyek berisiko
- `generate_trend_report(metric, period_range)` - Generate trend report
- `suggest_budget_reallocation(project_code)` - Saran realokasi budget
- `export_analytics(analysis_type, format)` - Export hasil analisis

**Inter-Agent Dependencies:**
- Mengakses data dari semua agent operasional
- Berkoordinasi dengan Project Management Agent untuk context
- Menyediakan insights ke Orchestrator Agent untuk decision making
- Mengirim alert ke Notification Agent untuk anomali terdeteksi

---

## Inter-Agent Workflows

### Workflow 1: Proposal Approval Process

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Project   │────►│  Proposal   │────►│   Budget    │────►│Notification │
│  Manager    │     │ Processing  │     │ Allocation  │     │   Agent     │
│  (User)     │     │   Agent     │     │   Agent     │     │             │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
      │                    │                   │                   │
      │ Create Draft       │ Validate Budget   │ Check Available   │ Notify FM
      │                    │                   │                   │
      ▼                    ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Submit    │────►│  Submitted  │────►│   Budget    │────►│   Finance   │
│  Proposal   │     │   Status    │     │   Reserved  │     │  Manager    │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
                                                                   │
                           ┌───────────────────────────────────────┤
                           │                                       │
                           ▼                                       ▼
                    ┌─────────────┐                         ┌─────────────┐
                    │  Approved   │                         │  Rejected   │
                    │   Status    │                         │   Status    │
                    └─────────────┘                         └─────────────┘
                           │                                       │
                           ▼                                       ▼
                    ┌─────────────┐                         ┌─────────────┐
                    │   Budget    │                         │   Budget    │
                    │ Allocation  │                         │  Released   │
                    │  Confirmed  │                         │             │
                    └─────────────┘                         └─────────────┘
```

**Sequence:**
1. PM creates proposal draft via Proposal Processing Agent
2. Proposal Processing Agent validates data completeness
3. PM submits proposal
4. Proposal Processing Agent requests Budget Allocation Agent to check availability
5. Budget Allocation Agent reserves tentative allocation
6. Notification Agent notifies FM of pending approval
7. FM reviews and approves/rejects
8. If approved: Budget Allocation Agent confirms allocation
9. If rejected: Budget Allocation Agent releases reservation
10. Notification Agent notifies PM of decision
11. Audit Agent logs entire workflow

---

### Workflow 2: Financial Report Approval

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Project   │────►│  Financial  │────►│Notification │────►│    Staff    │
│  Manager    │     │  Reporting  │     │   Agent     │     │ Accountant  │
│  (User)     │     │   Agent     │     │             │     │   (SA)      │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
      │                    │                                       │
      │ Create & Submit    │ Status: Submitted                     │ Review
      │                    │                                       │
      ▼                    ▼                                       ▼
┌─────────────┐     ┌─────────────┐                         ┌─────────────┐
│   Draft     │────►│ Submitted   │◄────────────────────────│  Verified   │
│  Report     │     │  Status     │                         │   Status    │
└─────────────┘     └─────────────┘                         └─────────────┘
                                                                   │
                                                                   ▼
                                                            ┌─────────────┐
                                                            │Notification │
                                                            │   Agent     │
                                                            └─────────────┘
                                                                   │
                                                                   ▼
                                                            ┌─────────────┐
                                                            │   Finance   │
                                                            │  Manager    │
                                                            └─────────────┘
                                                                   │
                           ┌───────────────┬───────────────────────┤
                           │               │                       │
                           ▼               ▼                       ▼
                    ┌─────────────┐ ┌─────────────┐         ┌─────────────┐
                    │  Approved   │ │  Revision   │         │  Rejected   │
                    │   Status    │ │  Requested  │         │   Status    │
                    └─────────────┘ └─────────────┘         └─────────────┘
                           │               │
                           ▼               ▼
                    ┌─────────────┐ ┌─────────────┐
                    │ Bank Trx    │ │  PM Revise  │────► (Back to Draft)
                    │   Agent     │ │             │
                    │ (Record)    │ └─────────────┘
                    └─────────────┘
```

**Sequence:**
1. PM creates report via Financial Reporting Agent
2. PM adds details and attaches receipts
3. PM submits report (status: Submitted)
4. Notification Agent notifies SA for verification
5. SA reviews and verifies (status: Verified)
6. Notification Agent notifies FM for approval
7. FM reviews and decides:
   - Approve: Financial Reporting Agent updates status, Bank Transaction Agent records
   - Request Revision: PM gets notification to revise, returns to draft
   - Reject: Report is rejected with reason
8. Audit Agent logs entire workflow

---

### Workflow 3: Monthly Bank Book Closing

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│Orchestrator │────►│    Bank     │────►│ Receivables │────►│  Financial  │
│   Agent     │     │ Transaction │     │ Management  │     │  Reporting  │
│ (Scheduled) │     │   Agent     │     │   Agent     │     │   Agent     │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
      │                    │                   │                   │
      │ Trigger Month-End  │ Close Period      │ Close Period      │ Consolidate
      │                    │                   │                   │
      ▼                    ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Schedule   │────►│  Calculate  │────►│  Calculate  │────►│   Monthly   │
│  Trigger    │     │   Balance   │     │   Balance   │     │   Report    │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
                           │                   │                   │
                           ▼                   ▼                   ▼
                    ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
                    │ Notify SA   │     │ Notify SA   │     │ Notify FM   │
                    │ for Review  │     │ for Review  │     │ for Review  │
                    └─────────────┘     └─────────────┘     └─────────────┘
                           │                   │                   │
                           └───────────────────┴───────────────────┘
                                               │
                                               ▼
                                        ┌─────────────┐
                                        │   Donor     │
                                        │  Reporting  │
                                        │   Agent     │
                                        └─────────────┘
```

**Sequence:**
1. Orchestrator Agent triggers month-end process on scheduled date
2. Bank Transaction Agent calculates final balance for all project bank books
3. Receivables Management Agent calculates period-end receivables
4. Financial Reporting Agent consolidates all data
5. Notification Agent notifies SA to review bank books and receivables
6. After SA verification, FM is notified for final approval
7. Upon FM approval, Donor Reporting Agent prepares donor reports if needed
8. Audit Agent logs entire closing process

---

### Workflow 4: Budget Alert & Reallocation

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Budget    │────►│Notification │────►│   Finance   │────►│   Budget    │
│ Allocation  │     │   Agent     │     │  Manager    │     │ Allocation  │
│   Agent     │     │             │     │   (User)    │     │   Agent     │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
      │                    │                   │                   │
      │ Detect 80% Used    │ Alert FM          │ Review & Decide   │ Reallocate
      │                    │                   │                   │
      ▼                    ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Threshold  │────►│   Budget    │────►│  Approve    │────►│  Transfer   │
│   Reached   │     │   Alert     │     │  Realloc    │     │   Budget    │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
                                                                   │
                                                                   ▼
                                                            ┌─────────────┐
                                                            │    Data     │
                                                            │  Analytics  │
                                                            │   Agent     │
                                                            └─────────────┘
                                                                   │
                                                                   ▼
                                                            ┌─────────────┐
                                                            │  Update     │
                                                            │  Forecasts  │
                                                            └─────────────┘
```

**Sequence:**
1. Budget Allocation Agent monitors utilization rates
2. When 80% threshold reached, triggers alert
3. Notification Agent sends alert to FM with details
4. Data Analytics Agent provides recommendations for reallocation
5. FM reviews and approves reallocation
6. Budget Allocation Agent executes transfer between allocations
7. Data Analytics Agent updates forecasts
8. Audit Agent logs reallocation with justification

---

## Communication Protocol

### Message Format

Semua komunikasi antar agent menggunakan format standar:

```json
{
  "message_id": "uuid-v4",
  "timestamp": "ISO-8601",
  "source_agent": "agent-identifier",
  "target_agent": "agent-identifier",
  "message_type": "request|response|event|command",
  "priority": "low|normal|high|critical",
  "correlation_id": "original-message-id-for-responses",
  "payload": {
    "action": "action-name",
    "parameters": {},
    "context": {}
  },
  "metadata": {
    "user_id": "triggering-user-if-any",
    "session_id": "session-identifier",
    "trace_id": "distributed-trace-id"
  }
}
```

### Priority Levels

| Priority | Description | SLA Response |
|----------|-------------|--------------|
| Critical | Security breach, system failure | < 1 minute |
| High | Approval deadlines, budget alerts | < 5 minutes |
| Normal | Standard operations | < 30 minutes |
| Low | Analytics, reports | < 4 hours |

### Event Types

| Event | Publisher | Subscribers |
|-------|-----------|-------------|
| `proposal.submitted` | Proposal Processing | Notification, Budget Allocation |
| `proposal.approved` | Proposal Processing | Budget Allocation, Bank Transaction, Notification |
| `report.submitted` | Financial Reporting | Notification, Audit |
| `report.verified` | Financial Reporting | Notification |
| `report.approved` | Financial Reporting | Bank Transaction, Notification |
| `budget.threshold_reached` | Budget Allocation | Notification, Data Analytics |
| `user.login` | User Management | Audit |
| `user.action` | All Agents | Audit |

---

## Error Handling & Recovery

### Error Categories

1. **Validation Errors** (4xx)
   - Handled by source agent
   - User notified immediately
   - No retry needed

2. **Processing Errors** (5xx)
   - Logged by Audit Agent
   - Retry with exponential backoff (3 attempts)
   - Escalate to Orchestrator if all retries fail

3. **Integration Errors**
   - External service failures
   - Circuit breaker pattern (5 failures = open)
   - Fallback to cached data if available

4. **Business Rule Violations**
   - Logged as compliance event
   - Notify relevant approvers
   - Block operation until resolved

### Recovery Procedures

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Error     │────►│   Retry     │────►│  Escalate   │
│  Detected   │     │ (3 times)   │     │  to Human   │
└─────────────┘     └─────────────┘     └─────────────┘
      │                    │                   │
      ▼                    ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│    Log      │     │  Success?   │     │ Admin       │
│   Audit     │     │   Resume    │     │ Dashboard   │
└─────────────┘     └─────────────┘     └─────────────┘
```

---

## Security Considerations

### Authentication & Authorization

1. **Agent-to-Agent Authentication**
   - Mutual TLS for inter-agent communication
   - Service accounts with rotated credentials
   - JWT tokens for short-lived sessions

2. **User Actions**
   - All agent actions triggered by users carry user context
   - Role-based access control enforced at each agent
   - Audit trail maintained for all actions

### Data Protection

1. **Encryption**
   - At-rest: AES-256 for database and file storage
   - In-transit: TLS 1.3 for all communications
   - Sensitive fields: Additional field-level encryption for passwords, OTP

2. **Data Access**
   - Principle of least privilege for each agent
   - Data masking in logs and analytics
   - Retention policies per data classification

### Compliance

1. **Audit Requirements**
   - Complete audit trail for all financial transactions
   - Tamper-proof logging with cryptographic signatures
   - Retention for 7 years per standard accounting requirements

2. **Segregation of Duties**
   - Enforced by User Management Agent
   - PM cannot approve own proposals
   - SA cannot approve own reports
   - FM review required for all financial decisions

---

## Appendix A: Entity-Agent Mapping

| Entity | Primary Agent | Secondary Agents |
|--------|---------------|------------------|
| User | User Management | Notification, Audit |
| Proyek | Project Management | Budget Allocation, Donor Reporting |
| Village | Project Management | Budget Allocation |
| ProjectCodeBudget | Budget Allocation | Proposal Processing, Bank Transaction |
| Proposal | Proposal Processing | Budget Allocation, Notification |
| ProposalBudgetDetail | Proposal Processing | Budget Allocation |
| BukuBankHeader | Bank Transaction | Financial Reporting, Receivables |
| BukuBankDetail | Bank Transaction | Audit |
| BukuPiutangHeader | Receivables Management | Financial Reporting |
| BukuPiutangDetail | Receivables Management | Audit |
| LaporanKeuanganHeader | Financial Reporting | Donor Reporting, Audit |
| LaporanKeuanganDetail | Financial Reporting | Audit |
| LaporanDonor | Donor Reporting | Project Management, Audit |
| OtpCode | User Management | Notification |

---

## Appendix B: Status Workflow Summary

### Proposal Status Workflow
```
Draft ──► Submitted ──┬──► ApprovedFM ──► (Approved)
                      │
                      └──► Rejected
```

### Report Status Workflow
```
Draft ──► Submitted ──► Verified ──┬──► Approved
   ▲                               │
   │                               ├──► Rejected
   │                               │
   └────── RevisionRequested ◄─────┘
```

### Bank Book Status Workflow
```
Draft ──► Submitted ──► Approved
```

---

## Appendix C: Role-Agent Permission Matrix

| Agent | Admin | Finance Manager | Project Manager | Staff Accountant | Direktur |
|-------|-------|-----------------|-----------------|------------------|----------|
| User Management | Full | Read | Read Own | Read Own | Read |
| Project Management | Full | Read/Update | Read Own Projects | Read | Read |
| Proposal Processing | Read | Approve/Reject | Create/Submit | Read | Read |
| Budget Allocation | Full | Full | Read Own | Read | Read |
| Bank Transaction | Read | Approve | Create/Submit | Verify | Read |
| Receivables Management | Read | Approve | Create/Submit | Verify | Read |
| Financial Reporting | Read | Approve | Create/Submit | Verify | Read |
| Donor Reporting | Read | Create/Approve | Read | Read | Read |
| Notification | Manage | Receive | Receive | Receive | Receive |
| Audit & Compliance | Full | Read | Read Own | Read | Full |
| Data Analytics | Full | Full | Own Data | Own Data | Full |

---

*Document Version: 1.0*
*Last Updated: 2026-01-06*
*Author: AI Architect System*
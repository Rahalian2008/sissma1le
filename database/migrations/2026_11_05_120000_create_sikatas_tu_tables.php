<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Employees Table
        if (! Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nip', 30)->nullable()->index();
                $table->string('ni_pppk', 30)->nullable();
                $table->string('pppk_contract_number', 100)->nullable();
                $table->date('pppk_contract_start')->nullable();
                $table->date('pppk_contract_end')->nullable();
                $table->string('nuptk', 30)->nullable();
                $table->string('nik', 30)->nullable();
                $table->string('karpeg_number', 50)->nullable();
                $table->string('taspen_number', 50)->nullable();
                $table->string('bpjs_number', 50)->nullable();
                $table->string('npwp', 50)->nullable();
                $table->string('name');
                $table->string('title_prefix', 20)->nullable();
                $table->string('title_suffix', 30)->nullable();
                $table->enum('gender', ['L', 'P'])->default('L');
                $table->string('birth_place', 100)->nullable();
                $table->date('birth_date')->nullable();
                $table->string('employment_type', 50)->default('Guru');
                $table->string('employment_status', 50)->default('PNS');
                $table->string('rank_group', 20)->nullable();
                $table->date('rank_tmt')->nullable();
                $table->date('last_kp_tmt')->nullable();
                $table->date('next_kp_tmt')->nullable();
                $table->date('pension_tmt')->nullable();
                $table->string('pension_status', 50)->nullable();
                $table->string('pension_sk_number', 100)->nullable();
                $table->text('pension_notes')->nullable();
                $table->string('kp_status', 50)->nullable();
                $table->string('position', 100)->nullable();
                $table->string('homeroom_rombel', 50)->nullable();
                $table->date('position_tmt')->nullable();
                $table->date('last_kgb_tmt')->nullable();
                $table->date('next_kgb_tmt')->nullable();
                $table->decimal('last_kgb_salary', 14, 2)->default(0);
                $table->integer('years_of_service_years')->default(0);
                $table->integer('years_of_service_months')->default(0);
                $table->string('education_level', 20)->nullable();
                $table->string('education_major', 100)->nullable();
                $table->string('education_institution', 150)->nullable();
                $table->integer('education_grad_year')->nullable();
                $table->boolean('is_certified')->default(false);
                $table->string('teaching_subject', 100)->nullable();
                $table->string('additional_tasks', 150)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('address')->nullable();
                $table->string('photo_path', 255)->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('leave_balance')->default(12);
                $table->timestamps();
            });
        }

        // 2. Employee Leaves Table
        if (! Schema::hasTable('employee_leaves')) {
            Schema::create('employee_leaves', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('leave_type', 50);
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('days_count')->default(1);
                $table->text('reason')->nullable();
                $table->text('address_during_leave')->nullable();
                $table->string('phone_during_leave', 30)->nullable();
                $table->foreignId('replacement_employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->string('letter_number', 100)->nullable();
                $table->string('status', 50)->default('Menunggu');
                $table->text('notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->string('attachment_path', 255)->nullable();
                $table->timestamps();
            });
        }

        // 3. Incoming Letters
        if (! Schema::hasTable('incoming_letters')) {
            Schema::create('incoming_letters', function (Blueprint $table) {
                $table->id();
                $table->string('agenda_number', 50)->nullable();
                $table->string('letter_number', 100);
                $table->string('sender', 150);
                $table->date('letter_date');
                $table->date('received_date');
                $table->string('subject');
                $table->string('classification_code', 50)->nullable();
                $table->string('character', 50)->default('Biasa');
                $table->integer('attachment_count')->default(0);
                $table->string('file_path', 255)->nullable();
                $table->string('status', 50)->default('Diterima');
                $table->unsignedBigInteger('guest_id')->nullable();
                $table->timestamps();
            });
        }

        // 4. Outgoing Letters
        if (! Schema::hasTable('outgoing_letters')) {
            Schema::create('outgoing_letters', function (Blueprint $table) {
                $table->id();
                $table->string('agenda_number', 50)->nullable();
                $table->string('letter_number', 100);
                $table->string('recipient', 150);
                $table->date('letter_date');
                $table->string('subject');
                $table->string('classification_code', 50)->nullable();
                $table->string('signer_name', 100)->nullable();
                $table->string('signer_nip', 30)->nullable();
                $table->string('file_path', 255)->nullable();
                $table->string('status', 50)->default('Konsep');
                $table->timestamps();
            });
        }

        // 5. Dispositions
        if (! Schema::hasTable('dispositions')) {
            Schema::create('dispositions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('incoming_letter_id')->constrained('incoming_letters')->cascadeOnDelete();
                $table->foreignId('given_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('target_role_or_user', 100)->nullable();
                $table->date('disposition_date');
                $table->text('instructions')->nullable();
                $table->date('due_date')->nullable();
                $table->text('follow_up_notes')->nullable();
                $table->string('status', 50)->default('Menunggu');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // 6. Rooms
        if (! Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->nullable();
                $table->string('name', 100);
                $table->string('person_in_charge', 100)->nullable();
                $table->integer('capacity')->default(0);
                $table->string('condition', 50)->default('Baik');
                $table->timestamps();
            });
        }

        // 7. Assets
        if (! Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->nullable()->index();
                $table->string('name');
                $table->string('brand_model', 100)->nullable();
                $table->text('specification')->nullable();
                $table->string('serial_number', 100)->nullable();
                $table->integer('procurement_year')->nullable();
                $table->string('funding_source', 100)->nullable();
                $table->decimal('price', 14, 2)->default(0);
                $table->string('unit', 30)->default('Unit');
                $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
                $table->string('kib_type', 10)->default('B');
                $table->string('sim_bmd_code', 50)->nullable();
                $table->string('register_number', 50)->nullable();
                $table->string('condition', 50)->default('Baik');
                $table->string('qr_code_token', 100)->nullable();
                $table->string('photo_path', 255)->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('goods_receipt_id')->nullable();
                $table->timestamps();
            });
        }

        // 8. Goods
        if (! Schema::hasTable('goods')) {
            Schema::create('goods', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->nullable()->index();
                $table->string('name');
                $table->string('category', 100)->nullable();
                $table->string('item_type', 50)->default('BHP');
                $table->string('kib_type', 10)->nullable();
                $table->string('sim_bmd_code', 50)->nullable();
                $table->string('unit', 30)->default('Pcs');
                $table->integer('current_stock')->default(0);
                $table->integer('minimum_stock')->default(0);
                $table->string('storage_location', 100)->nullable();
                $table->timestamps();
            });
        }

        // 9. Goods Receipts
        if (! Schema::hasTable('goods_receipts')) {
            Schema::create('goods_receipts', function (Blueprint $table) {
                $table->id();
                $table->string('receipt_number', 100)->nullable();
                $table->date('receipt_date');
                $table->string('supplier_name', 150)->nullable();
                $table->string('funding_source', 100)->nullable();
                $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('total_amount', 14, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 10. Goods Borrowings
        if (! Schema::hasTable('goods_borrowings')) {
            Schema::create('goods_borrowings', function (Blueprint $table) {
                $table->id();
                $table->string('borrowing_number', 100)->nullable();
                $table->string('borrower_name', 150);
                $table->string('borrower_type', 50)->default('Guru');
                $table->string('good_or_asset_name');
                $table->integer('quantity')->default(1);
                $table->date('borrow_date');
                $table->date('expected_return_date')->nullable();
                $table->date('actual_return_date')->nullable();
                $table->string('status', 50)->default('Dipinjam');
                $table->string('condition_on_return', 50)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 11. Budgets
        if (! Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->string('fiscal_year', 20);
                $table->string('funding_source', 100)->default('BOS Reguler');
                $table->decimal('total_allocation', 16, 2)->default(0);
                $table->text('description')->nullable();
                $table->boolean('is_approved')->default(true);
                $table->timestamps();
            });
        }

        // 12. Budget Items
        if (! Schema::hasTable('budget_items')) {
            Schema::create('budget_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
                $table->string('account_code', 50)->nullable();
                $table->string('program_name', 150)->nullable();
                $table->string('activity_name', 150);
                $table->decimal('allocated_amount', 14, 2)->default(0);
                $table->decimal('realized_amount', 14, 2)->default(0);
                $table->timestamps();
            });
        }

        // 13. Cash Transactions
        if (! Schema::hasTable('cash_transactions')) {
            Schema::create('cash_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('evidence_number', 100)->nullable();
                $table->date('transaction_date');
                $table->enum('type', ['Penerimaan', 'Pengeluaran'])->default('Pengeluaran');
                $table->string('funding_source', 100)->default('BOS Reguler');
                $table->foreignId('budget_item_id')->nullable()->constrained('budget_items')->nullOnDelete();
                $table->unsignedBigInteger('goods_receipt_id')->nullable();
                $table->unsignedBigInteger('asset_id')->nullable();
                $table->string('account_code', 50)->nullable();
                $table->text('description')->nullable();
                $table->string('recipient_or_payer', 150)->nullable();
                $table->decimal('debit', 14, 2)->default(0);
                $table->decimal('credit', 14, 2)->default(0);
                $table->decimal('running_balance', 14, 2)->default(0);
                $table->string('payment_method', 50)->default('Kas Tunai');
                $table->string('spk_number', 100)->nullable();
                $table->string('invoice_number', 100)->nullable();
                $table->string('receipt_document_path', 255)->nullable();
                $table->boolean('is_verified')->default(false);
                $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 14. Student Mutations
        if (! Schema::hasTable('student_mutations')) {
            Schema::create('student_mutations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->enum('mutation_type', ['Masuk', 'Keluar', 'Pindah Kelas', 'Drop Out'])->default('Keluar');
                $table->string('document_number', 100)->nullable();
                $table->date('mutation_date');
                $table->string('school_target_or_origin', 150)->nullable();
                $table->text('reason')->nullable();
                $table->string('supporting_document_path', 255)->nullable();
                $table->string('status', 50)->default('Ditetapkan');
                $table->string('approved_by', 100)->nullable();
                $table->timestamps();
            });
        }

        // 15. Student Diplomas
        if (! Schema::hasTable('student_diplomas')) {
            Schema::create('student_diplomas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->string('diploma_number', 100)->nullable();
                $table->string('skl_number', 100)->nullable();
                $table->string('graduation_year', 20)->nullable();
                $table->date('handover_date')->nullable();
                $table->string('recipient_name', 100)->nullable();
                $table->string('recipient_relation', 50)->nullable();
                $table->string('three_fingerprint_status', 50)->default('Sudah');
                $table->string('status', 50)->default('Tersimpan di Sekolah');
                $table->string('handed_over_by', 100)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 16. Student Families
        if (! Schema::hasTable('student_families')) {
            Schema::create('student_families', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->string('father_name', 100)->nullable();
                $table->string('father_nik', 30)->nullable();
                $table->string('father_education', 50)->nullable();
                $table->string('father_occupation', 100)->nullable();
                $table->string('father_income', 50)->nullable();
                $table->string('father_phone', 30)->nullable();
                $table->string('mother_name', 100)->nullable();
                $table->string('mother_nik', 30)->nullable();
                $table->string('mother_education', 50)->nullable();
                $table->string('mother_occupation', 100)->nullable();
                $table->string('mother_income', 50)->nullable();
                $table->string('mother_phone', 30)->nullable();
                $table->string('guardian_name', 100)->nullable();
                $table->string('guardian_nik', 30)->nullable();
                $table->string('guardian_education', 50)->nullable();
                $table->string('guardian_occupation', 100)->nullable();
                $table->string('guardian_income', 50)->nullable();
                $table->string('guardian_phone', 30)->nullable();
                $table->timestamps();
            });
        }

        // 17. Student Alumni
        if (! Schema::hasTable('student_alumni')) {
            Schema::create('student_alumni', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->string('graduation_year', 20)->nullable();
                $table->string('activity_status', 50)->default('Kuliah');
                $table->string('campus_name', 150)->nullable();
                $table->string('major', 100)->nullable();
                $table->string('company_name', 150)->nullable();
                $table->string('job_title', 100)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('address')->nullable();
                $table->timestamps();
            });
        }

        // 18. PPDB Registrations
        if (! Schema::hasTable('ppdb_registrations')) {
            Schema::create('ppdb_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('registration_number', 50)->unique();
                $table->string('full_name', 150);
                $table->string('nisn', 20)->nullable();
                $table->string('nik', 30)->nullable();
                $table->enum('gender', ['L', 'P'])->default('L');
                $table->string('birth_place', 100)->nullable();
                $table->date('birth_date')->nullable();
                $table->string('school_origin', 150)->nullable();
                $table->string('parent_name', 100)->nullable();
                $table->string('parent_phone', 30)->nullable();
                $table->decimal('average_score', 5, 2)->default(0);
                $table->string('selection_track', 50)->default('Zonasi');
                $table->string('status', 50)->default('Menunggu');
                $table->string('assigned_class', 50)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 19. Asset Disposals
        if (! Schema::hasTable('asset_disposals')) {
            Schema::create('asset_disposals', function (Blueprint $table) {
                $table->id();
                $table->string('disposal_number', 100)->nullable();
                $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
                $table->date('disposal_date');
                $table->text('reason')->nullable();
                $table->decimal('estimated_scrap_value', 14, 2)->default(0);
                $table->text('chronology_description')->nullable();
                $table->string('proposed_by_name', 100)->nullable();
                $table->string('inspector_names', 255)->nullable();
                $table->string('official_report_number', 100)->nullable();
                $table->string('status', 50)->default('Diusulkan');
                $table->timestamp('approved_at')->nullable();
                $table->string('file_path', 255)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 20. Asset Maintenances
        if (! Schema::hasTable('asset_maintenances')) {
            Schema::create('asset_maintenances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
                $table->date('request_date');
                $table->text('issue_description')->nullable();
                $table->text('action_plan')->nullable();
                $table->decimal('estimated_cost', 14, 2)->default(0);
                $table->decimal('actual_cost', 14, 2)->default(0);
                $table->string('technician_or_vendor', 150)->nullable();
                $table->string('status', 50)->default('Diajukan');
                $table->date('completed_date')->nullable();
                $table->timestamps();
            });
        }

        // 21. Stock Opnames
        if (! Schema::hasTable('stock_opnames')) {
            Schema::create('stock_opnames', function (Blueprint $table) {
                $table->id();
                $table->date('opname_date');
                $table->string('inspector_name', 100)->nullable();
                $table->foreignId('good_id')->constrained('goods')->cascadeOnDelete();
                $table->integer('system_stock')->default(0);
                $table->integer('physical_stock')->default(0);
                $table->integer('difference')->default(0);
                $table->text('explanation')->nullable();
                $table->string('official_report_number', 100)->nullable();
                $table->timestamps();
            });
        }

        // 22. Goods Issuances & Items
        if (! Schema::hasTable('goods_issuances')) {
            Schema::create('goods_issuances', function (Blueprint $table) {
                $table->id();
                $table->string('issuance_number', 100)->nullable();
                $table->date('issuance_date');
                $table->string('recipient_name', 150);
                $table->string('purpose', 255)->nullable();
                $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('goods_issuance_items')) {
            Schema::create('goods_issuance_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('goods_issuance_id')->constrained('goods_issuances')->cascadeOnDelete();
                $table->foreignId('good_id')->constrained('goods')->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->timestamps();
            });
        }

        // 23. Goods Receipt Items
        if (! Schema::hasTable('goods_receipt_items')) {
            Schema::create('goods_receipt_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
                $table->foreignId('good_id')->nullable()->constrained('goods')->nullOnDelete();
                $table->string('item_type', 50)->default('BHP');
                $table->string('kib_type', 10)->nullable();
                $table->string('sim_bmd_code', 50)->nullable();
                $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
                $table->string('brand_model', 150)->nullable();
                $table->string('serial_number', 100)->nullable();
                $table->text('specification')->nullable();
                $table->string('condition', 50)->default('Baik');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 14, 2)->default(0);
                $table->decimal('total_price', 14, 2)->default(0);
                $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 24. Tax Records
        if (! Schema::hasTable('tax_records')) {
            Schema::create('tax_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cash_transaction_id')->constrained('cash_transactions')->cascadeOnDelete();
                $table->string('tax_type', 50)->default('PPN');
                $table->decimal('tax_base', 14, 2)->default(0);
                $table->decimal('rate_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 14, 2)->default(0);
                $table->string('ntpn', 50)->nullable();
                $table->string('billing_code', 50)->nullable();
                $table->date('deposit_date')->nullable();
                $table->string('status', 50)->default('Disetor');
                $table->timestamps();
            });
        }

        // 25. Letter Expeditions
        if (! Schema::hasTable('letter_expeditions')) {
            Schema::create('letter_expeditions', function (Blueprint $table) {
                $table->id();
                $table->string('expedition_number', 100)->nullable();
                $table->foreignId('outgoing_letter_id')->nullable()->constrained('outgoing_letters')->nullOnDelete();
                $table->string('letter_number', 100);
                $table->date('letter_date');
                $table->date('delivery_date');
                $table->string('recipient_name', 150);
                $table->string('destination_type', 50)->default('Internal');
                $table->string('subject');
                $table->string('courier_name', 100)->nullable();
                $table->string('receiver_name', 100)->nullable();
                $table->string('receipt_status', 50)->default('Terkirim');
                $table->timestamp('received_at')->nullable();
                $table->string('receipt_file_path', 255)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 26. Letter Templates
        if (! Schema::hasTable('letter_templates')) {
            Schema::create('letter_templates', function (Blueprint $table) {
                $table->id();
                $table->string('title', 150);
                $table->string('category', 50)->default('Umum');
                $table->text('description')->nullable();
                $table->string('code', 50)->unique();
                $table->longText('body_content')->nullable();
                $table->timestamps();
            });
        }

        // 27. Meeting Notes
        if (! Schema::hasTable('meeting_notes')) {
            Schema::create('meeting_notes', function (Blueprint $table) {
                $table->id();
                $table->string('meeting_number', 100)->nullable();
                $table->string('meeting_title', 200);
                $table->string('meeting_type', 50)->default('Rapat Dinas');
                $table->date('meeting_date');
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('location', 150)->nullable();
                $table->string('chairperson_name', 100)->nullable();
                $table->string('secretary_name', 100)->nullable();
                $table->integer('attendees_count')->default(0);
                $table->text('agenda_topics')->nullable();
                $table->longText('discussion_summary')->nullable();
                $table->longText('decisions_and_follow_up')->nullable();
                $table->string('attendance_file_path', 255)->nullable();
                $table->string('status', 50)->default('Final');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 28. SPPDs
        if (! Schema::hasTable('sppds')) {
            Schema::create('sppds', function (Blueprint $table) {
                $table->id();
                $table->string('sppd_number', 100)->nullable();
                $table->foreignId('outgoing_letter_id')->nullable()->constrained('outgoing_letters')->nullOnDelete();
                $table->string('letter_task_number', 100)->nullable();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('assignment_purpose', 255);
                $table->string('transportation_type', 50)->default('Kendaraan Dinas');
                $table->string('departure_location', 100)->default('SMA Negeri 1 Lengkong');
                $table->string('destination_location', 100);
                $table->integer('duration_days')->default(1);
                $table->date('departure_date');
                $table->date('return_date');
                $table->string('budget_source', 100)->default('BOS Reguler');
                $table->string('budget_account', 50)->nullable();
                $table->text('followers')->nullable();
                $table->text('notes')->nullable();
                $table->string('ppk_name', 100)->nullable();
                $table->string('ppk_nip', 30)->nullable();
                $table->string('status', 50)->default('Ditetapkan');
                $table->timestamps();
            });
        }

        // 29. Guests
        if (! Schema::hasTable('guests')) {
            Schema::create('guests', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number', 50)->unique();
                $table->string('name', 150);
                $table->enum('gender', ['L', 'P'])->default('L');
                $table->string('identity_type', 50)->default('KTP');
                $table->string('identity_number', 50)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('institution', 150)->nullable();
                $table->string('institution_type', 50)->default('Instansi Pemerintah');
                $table->string('destination_department', 100)->nullable();
                $table->string('destination_person_name', 100)->nullable();
                $table->string('purpose', 255);
                $table->text('purpose_detail')->nullable();
                $table->integer('number_of_guests')->default(1);
                $table->string('vehicle_type', 50)->nullable();
                $table->string('vehicle_license_plate', 30)->nullable();
                $table->string('selfie_path', 255)->nullable();
                $table->string('signature_path', 255)->nullable();
                $table->string('status', 50)->default('Selesai');
                $table->timestamp('check_in_at')->nullable();
                $table->timestamp('check_out_at')->nullable();
                $table->string('qr_code_token', 100)->nullable();
                $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 30. Archives
        if (! Schema::hasTable('archives')) {
            Schema::create('archives', function (Blueprint $table) {
                $table->id();
                $table->string('archive_number', 100)->nullable();
                $table->string('title', 200);
                $table->string('category', 50)->default('Umum');
                $table->string('classification_code', 50)->nullable();
                $table->string('origin_unit', 100)->default('Tata Usaha');
                $table->integer('year')->default(2025);
                $table->string('archive_status', 50)->default('Aktif');
                $table->integer('retention_period_years')->default(5);
                $table->date('retention_expire_date')->nullable();
                $table->string('location_rack', 100)->nullable();
                $table->string('file_path', 255)->nullable();
                $table->string('qr_code_token', 100)->nullable();
                $table->string('source_type', 100)->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 31. Organization Structures
        if (! Schema::hasTable('organization_structures')) {
            Schema::create('organization_structures', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->nullable()->constrained('organization_structures')->nullOnDelete();
                $table->string('position_name', 100);
                $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->string('officer_name', 100)->nullable();
                $table->string('officer_nip', 30)->nullable();
                $table->string('sk_number', 100)->nullable();
                $table->text('duties_and_functions')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_structures');
        Schema::dropIfExists('archives');
        Schema::dropIfExists('guests');
        Schema::dropIfExists('sppds');
        Schema::dropIfExists('meeting_notes');
        Schema::dropIfExists('letter_templates');
        Schema::dropIfExists('letter_expeditions');
        Schema::dropIfExists('tax_records');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_issuance_items');
        Schema::dropIfExists('goods_issuances');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('asset_disposals');
        Schema::dropIfExists('ppdb_registrations');
        Schema::dropIfExists('student_alumni');
        Schema::dropIfExists('student_families');
        Schema::dropIfExists('student_diplomas');
        Schema::dropIfExists('student_mutations');
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('goods_borrowings');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('goods');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('dispositions');
        Schema::dropIfExists('outgoing_letters');
        Schema::dropIfExists('incoming_letters');
        Schema::dropIfExists('employee_leaves');
        Schema::dropIfExists('employees');
    }
};

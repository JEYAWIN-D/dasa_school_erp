<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Ensure book_categories exists with description & code
        if (!Schema::hasTable('book_categories')) {
            Schema::create('book_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 50)->nullable()->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('book_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('book_categories', 'code')) {
                    $table->string('code', 50)->nullable()->unique();
                }
                if (!Schema::hasColumn('book_categories', 'description')) {
                    $table->text('description')->nullable();
                }
            });
        }

        // 2. Physical Spatial Racks
        if (!Schema::hasTable('library_racks')) {
            Schema::create('library_racks', function (Blueprint $table) {
                $table->id();
                $table->string('rack_number', 50)->unique();
                $table->string('name');
                $table->string('building', 100)->default('Main Campus');
                $table->string('floor', 50)->default('Ground Floor');
                $table->string('room', 100)->default('Central Library');
                $table->string('section', 100)->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedInteger('capacity')->default(100);
                $table->string('qr_code_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('book_categories')->nullOnDelete();
            });
        }

        // 3. Physical Spatial Shelves
        if (!Schema::hasTable('library_shelves')) {
            Schema::create('library_shelves', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('rack_id');
                $table->string('shelf_number', 50);
                $table->string('name')->nullable();
                $table->string('subject', 100)->nullable();
                $table->unsignedInteger('capacity')->default(25);
                $table->boolean('is_active')->default(true);
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('rack_id')->references('id')->on('library_racks')->cascadeOnDelete();
                $table->unique(['rack_id', 'shelf_number']);
            });
        }

        // 4. Library Suppliers / Vendors
        if (!Schema::hasTable('library_suppliers')) {
            Schema::create('library_suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email')->nullable();
                $table->string('gst_number', 30)->nullable();
                $table->text('address')->nullable();
                $table->string('city', 100)->nullable();
                $table->string('state', 100)->nullable();
                $table->string('pincode', 20)->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_account_no')->nullable();
                $table->string('ifsc_code', 30)->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. Update books table with enterprise fields
        if (Schema::hasTable('books')) {
            Schema::table('books', function (Blueprint $table) {
                if (!Schema::hasColumn('books', 'subtitle')) {
                    $table->string('subtitle')->nullable()->after('title');
                }
                if (!Schema::hasColumn('books', 'co_author')) {
                    $table->string('co_author')->nullable()->after('author');
                }
                if (!Schema::hasColumn('books', 'subject')) {
                    $table->string('subject')->nullable()->after('category_id');
                }
                if (!Schema::hasColumn('books', 'class_suitability')) {
                    $table->string('class_suitability')->nullable()->after('subject');
                }
                if (!Schema::hasColumn('books', 'description')) {
                    $table->text('description')->nullable()->after('class_suitability');
                }
                if (!Schema::hasColumn('books', 'current_value')) {
                    $table->decimal('current_value', 10, 2)->nullable()->after('purchase_price');
                }
                if (!Schema::hasColumn('books', 'supplier_id')) {
                    $table->unsignedBigInteger('supplier_id')->nullable()->after('current_value');
                }
                if (!Schema::hasColumn('books', 'bill_number')) {
                    $table->string('bill_number', 100)->nullable()->after('supplier_id');
                }
                if (!Schema::hasColumn('books', 'invoice_reference')) {
                    $table->string('invoice_reference', 100)->nullable()->after('bill_number');
                }
                if (!Schema::hasColumn('books', 'pages')) {
                    $table->unsignedInteger('pages')->nullable()->after('invoice_reference');
                }
                if (!Schema::hasColumn('books', 'rack_id')) {
                    $table->unsignedBigInteger('rack_id')->nullable()->after('location');
                }
                if (!Schema::hasColumn('books', 'shelf_id')) {
                    $table->unsignedBigInteger('shelf_id')->nullable()->after('rack_id');
                }
                if (!Schema::hasColumn('books', 'status')) {
                    $table->string('status', 30)->default('active')->after('is_available');
                }
            });
        }

        // 6. Procurement: Purchase Orders
        if (!Schema::hasTable('library_purchase_orders')) {
            Schema::create('library_purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_number', 50)->unique();
                $table->unsignedBigInteger('supplier_id');
                $table->date('order_date');
                $table->date('expected_delivery_date')->nullable();
                $table->string('status', 30)->default('draft'); // draft, submitted, approved, partially_received, completed, cancelled
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('grand_total', 12, 2)->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('supplier_id')->references('id')->on('library_suppliers')->restrictOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 7. Procurement: PO Items
        if (!Schema::hasTable('library_purchase_order_items')) {
            Schema::create('library_purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_order_id');
                $table->string('book_title');
                $table->string('isbn', 30)->nullable();
                $table->string('author')->nullable();
                $table->string('publisher')->nullable();
                $table->string('edition', 50)->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedInteger('quantity_ordered');
                $table->unsignedInteger('quantity_received')->default(0);
                $table->decimal('unit_price', 10, 2);
                $table->decimal('discount_percent', 5, 2)->default(0);
                $table->decimal('total_price', 10, 2);
                $table->timestamps();

                $table->foreign('purchase_order_id')->references('id')->on('library_purchase_orders')->cascadeOnDelete();
                $table->foreign('category_id')->references('id')->on('book_categories')->nullOnDelete();
            });
        }

        // 8. Physical Book Copies Master
        if (!Schema::hasTable('book_copies')) {
            Schema::create('book_copies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('book_id');
                $table->string('accession_number', 50)->unique();
                $table->string('barcode', 60)->unique();
                $table->string('rfid_tag', 60)->nullable()->unique();
                $table->unsignedInteger('copy_number')->default(1);
                $table->unsignedBigInteger('shelf_id')->nullable();
                $table->string('condition', 30)->default('good'); // new, good, minor_damage, major_damage, torn_pages, water_damage, lost, written_off
                $table->string('status', 30)->default('available'); // available, issued, reserved, maintenance, lost, damaged, disposed
                $table->boolean('is_reference_only')->default(false);
                $table->unsignedBigInteger('purchase_order_item_id')->nullable();
                $table->decimal('cost_price', 10, 2)->nullable();
                $table->text('remarks')->nullable();
                $table->timestamp('last_inspected_at')->nullable();
                $table->timestamps();

                $table->foreign('book_id')->references('id')->on('books')->cascadeOnDelete();
                $table->foreign('shelf_id')->references('id')->on('library_shelves')->nullOnDelete();
                $table->foreign('purchase_order_item_id')->references('id')->on('library_purchase_order_items')->nullOnDelete();
                $table->index(['book_id', 'status']);
                $table->index(['condition']);
            });
        }

        // 9. Goods Receipts (GRN)
        if (!Schema::hasTable('library_goods_receipts')) {
            Schema::create('library_goods_receipts', function (Blueprint $table) {
                $table->id();
                $table->string('grn_number', 50)->unique();
                $table->unsignedBigInteger('purchase_order_id')->nullable();
                $table->unsignedBigInteger('supplier_id');
                $table->string('invoice_number', 100);
                $table->date('invoice_date');
                $table->date('receipt_date');
                $table->unsignedBigInteger('received_by')->nullable();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->unsignedInteger('total_items_received')->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('purchase_order_id')->references('id')->on('library_purchase_orders')->nullOnDelete();
                $table->foreign('supplier_id')->references('id')->on('library_suppliers')->restrictOnDelete();
                $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 10. Goods Receipt Items
        if (!Schema::hasTable('library_goods_receipt_items')) {
            Schema::create('library_goods_receipt_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('goods_receipt_id');
                $table->unsignedBigInteger('purchase_order_item_id')->nullable();
                $table->unsignedBigInteger('book_id')->nullable();
                $table->unsignedInteger('quantity_received');
                $table->decimal('unit_cost', 10, 2);
                $table->string('accession_number_start', 50)->nullable();
                $table->string('accession_number_end', 50)->nullable();
                $table->string('condition_note')->nullable();
                $table->timestamps();

                $table->foreign('goods_receipt_id')->references('id')->on('library_goods_receipts')->cascadeOnDelete();
                $table->foreign('purchase_order_item_id')->references('id')->on('library_purchase_order_items')->nullOnDelete();
                $table->foreign('book_id')->references('id')->on('books')->nullOnDelete();
            });
        }

        // 11. Circulation updates on book_issues
        if (Schema::hasTable('book_issues')) {
            Schema::table('book_issues', function (Blueprint $table) {
                if (!Schema::hasColumn('book_issues', 'copy_id')) {
                    $table->unsignedBigInteger('copy_id')->nullable()->after('book_id');
                    $table->foreign('copy_id')->references('id')->on('book_copies')->nullOnDelete();
                }
                if (!Schema::hasColumn('book_issues', 'issue_receipt_no')) {
                    $table->string('issue_receipt_no', 50)->nullable()->unique()->after('notes');
                }
                if (!Schema::hasColumn('book_issues', 'return_receipt_no')) {
                    $table->string('return_receipt_no', 50)->nullable()->after('issue_receipt_no');
                }
                if (!Schema::hasColumn('book_issues', 'return_condition')) {
                    $table->string('return_condition', 50)->nullable()->after('return_receipt_no');
                }
                if (!Schema::hasColumn('book_issues', 'damage_fine')) {
                    $table->decimal('damage_fine', 8, 2)->default(0)->after('fine_amount');
                }
                if (!Schema::hasColumn('book_issues', 'lost_fine')) {
                    $table->decimal('lost_fine', 8, 2)->default(0)->after('damage_fine');
                }
                if (!Schema::hasColumn('book_issues', 'override_by')) {
                    $table->unsignedBigInteger('override_by')->nullable()->after('returned_to');
                    $table->foreign('override_by')->references('id')->on('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('book_issues', 'override_reason')) {
                    $table->text('override_reason')->nullable()->after('override_by');
                }
                if (!Schema::hasColumn('book_issues', 'inspection_notes')) {
                    $table->text('inspection_notes')->nullable()->after('override_reason');
                }
            });
        }

        // 12. Fine Payments Ledger
        if (!Schema::hasTable('library_fine_payments')) {
            Schema::create('library_fine_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('book_issue_id');
                $table->string('receipt_no', 50)->unique();
                $table->decimal('amount_paid', 8, 2);
                $table->decimal('waiver_amount', 8, 2)->default(0);
                $table->string('payment_mode', 30)->default('cash'); // cash, upi, card, bank_transfer, other
                $table->string('transaction_reference', 100)->nullable();
                $table->text('waiver_reason')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->unsignedBigInteger('received_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('book_issue_id')->references('id')->on('book_issues')->cascadeOnDelete();
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 13. Periodicals & Newspapers
        if (!Schema::hasTable('library_periodicals')) {
            Schema::create('library_periodicals', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('periodical_type', 30)->default('magazine'); // newspaper, magazine, journal, newsletter
                $table->string('frequency', 30)->default('monthly'); // daily, weekly, fortnightly, monthly, quarterly, annual
                $table->string('issn', 30)->nullable();
                $table->string('publisher')->nullable();
                $table->string('language', 50)->default('English');
                $table->unsignedBigInteger('rack_id')->nullable();
                $table->unsignedBigInteger('shelf_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('rack_id')->references('id')->on('library_racks')->nullOnDelete();
                $table->foreign('shelf_id')->references('id')->on('library_shelves')->nullOnDelete();
            });
        }

        // 14. Periodical Subscriptions
        if (!Schema::hasTable('library_subscriptions')) {
            Schema::create('library_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('periodical_id');
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->string('subscription_number', 50)->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('cost', 10, 2)->default(0);
                $table->unsignedInteger('copies_per_issue')->default(1);
                $table->boolean('auto_renew')->default(false);
                $table->string('status', 30)->default('active'); // active, expired, cancelled
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('periodical_id')->references('id')->on('library_periodicals')->cascadeOnDelete();
                $table->foreign('supplier_id')->references('id')->on('library_suppliers')->nullOnDelete();
            });
        }

        // 15. Periodical Daily Receipts
        if (!Schema::hasTable('library_periodical_receipts')) {
            Schema::create('library_periodical_receipts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('periodical_id');
                $table->unsignedBigInteger('subscription_id')->nullable();
                $table->date('issue_date');
                $table->string('volume_no', 30)->nullable();
                $table->string('issue_no', 30)->nullable();
                $table->date('received_date');
                $table->unsignedInteger('copies_received')->default(1);
                $table->unsignedBigInteger('received_by')->nullable();
                $table->string('condition', 30)->default('good');
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('periodical_id')->references('id')->on('library_periodicals')->cascadeOnDelete();
                $table->foreign('subscription_id')->references('id')->on('library_subscriptions')->nullOnDelete();
                $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
                $table->unique(['periodical_id', 'issue_date', 'issue_no']);
            });
        }

        // 16. Library Assets
        if (!Schema::hasTable('library_assets')) {
            Schema::create('library_assets', function (Blueprint $table) {
                $table->id();
                $table->string('asset_code', 50)->unique();
                $table->string('name');
                $table->string('category', 50); // furniture, computer, scanner, printer, projector, appliance, other
                $table->string('brand_model')->nullable();
                $table->string('serial_number', 100)->nullable();
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_cost', 10, 2)->default(0);
                $table->decimal('current_value', 10, 2)->default(0);
                $table->string('location_room', 100)->nullable();
                $table->string('status', 30)->default('working'); // working, under_maintenance, damaged, scrapped, disposed
                $table->date('warranty_until')->nullable();
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('supplier_id')->references('id')->on('library_suppliers')->nullOnDelete();
            });
        }

        // 17. Library Asset Maintenance
        if (!Schema::hasTable('library_asset_maintenance')) {
            Schema::create('library_asset_maintenance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id');
                $table->date('maintenance_date');
                $table->string('service_provider')->nullable();
                $table->decimal('cost', 10, 2)->default(0);
                $table->text('issue_description');
                $table->text('action_taken')->nullable();
                $table->date('next_scheduled_date')->nullable();
                $table->string('status', 30)->default('completed');
                $table->unsignedBigInteger('conducted_by')->nullable();
                $table->timestamps();

                $table->foreign('asset_id')->references('id')->on('library_assets')->cascadeOnDelete();
                $table->foreign('conducted_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 18. Library Damage Reports (Wastage & Loss)
        if (!Schema::hasTable('library_damage_reports')) {
            Schema::create('library_damage_reports', function (Blueprint $table) {
                $table->id();
                $table->string('report_number', 50)->unique();
                $table->unsignedBigInteger('book_copy_id')->nullable();
                $table->unsignedBigInteger('asset_id')->nullable();
                $table->string('incident_type', 50); // torn_pages, water_damaged, binding_broken, missing_pages, vandalized, lost_by_borrower, lost_in_library, natural_wear, other
                $table->unsignedBigInteger('reported_by')->nullable();
                $table->unsignedBigInteger('borrower_student_id')->nullable();
                $table->unsignedBigInteger('borrower_employee_id')->nullable();
                $table->date('reported_date');
                $table->text('damage_description');
                $table->string('action_proposed', 50)->default('repair'); // repair, rebind, fine_and_replace, write_off, dispose
                $table->decimal('repair_cost', 8, 2)->default(0);
                $table->decimal('fine_charged', 8, 2)->default(0);
                $table->string('status', 30)->default('pending_review'); // pending_review, under_repair, repaired, approved_for_writeoff, disposed
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->text('review_notes')->nullable();
                $table->timestamps();

                $table->foreign('book_copy_id')->references('id')->on('book_copies')->nullOnDelete();
                $table->foreign('asset_id')->references('id')->on('library_assets')->nullOnDelete();
                $table->foreign('reported_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('borrower_student_id')->references('id')->on('students')->nullOnDelete();
                $table->foreign('borrower_employee_id')->references('id')->on('employees')->nullOnDelete();
                $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 19. Library Disposal Records (Write-offs)
        if (!Schema::hasTable('library_disposal_records')) {
            Schema::create('library_disposal_records', function (Blueprint $table) {
                $table->id();
                $table->string('disposal_number', 50)->unique();
                $table->unsignedBigInteger('damage_report_id')->nullable();
                $table->unsignedBigInteger('book_copy_id')->nullable();
                $table->unsignedBigInteger('asset_id')->nullable();
                $table->string('disposal_method', 50)->default('recycle_paper'); // recycle_paper, scrap_sale, donation, destruction, other
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->date('disposal_date');
                $table->decimal('original_value', 10, 2)->default(0);
                $table->decimal('scrap_value_recovered', 10, 2)->default(0);
                $table->string('revenue_receipt_no', 50)->nullable();
                $table->text('justification');
                $table->string('witness_name')->nullable();
                $table->string('certificate_file_path')->nullable();
                $table->timestamps();

                $table->foreign('damage_report_id')->references('id')->on('library_damage_reports')->nullOnDelete();
                $table->foreign('book_copy_id')->references('id')->on('book_copies')->nullOnDelete();
                $table->foreign('asset_id')->references('id')->on('library_assets')->nullOnDelete();
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 20. Stock Verification Sessions
        if (!Schema::hasTable('library_stock_verification_sessions')) {
            Schema::create('library_stock_verification_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_code', 50)->unique();
                $table->string('title');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->string('status', 30)->default('in_progress'); // in_progress, completed, cancelled
                $table->unsignedInteger('total_expected_copies')->default(0);
                $table->unsignedInteger('total_scanned_copies')->default(0);
                $table->unsignedInteger('matched_count')->default(0);
                $table->unsignedInteger('missing_count')->default(0);
                $table->unsignedInteger('damaged_count')->default(0);
                $table->unsignedInteger('misplaced_count')->default(0);
                $table->unsignedBigInteger('initiated_by')->nullable();
                $table->unsignedBigInteger('completed_by')->nullable();
                $table->text('summary_notes')->nullable();
                $table->timestamps();

                $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
                $table->foreign('initiated_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('completed_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 21. Stock Verification Items
        if (!Schema::hasTable('library_stock_verification_items')) {
            Schema::create('library_stock_verification_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id');
                $table->unsignedBigInteger('book_copy_id');
                $table->unsignedBigInteger('scanned_shelf_id')->nullable();
                $table->unsignedBigInteger('expected_shelf_id')->nullable();
                $table->string('verification_status', 30)->default('verified_present'); // verified_present, missing, damaged, misplaced, found_unregistered
                $table->timestamp('scanned_at');
                $table->unsignedBigInteger('scanned_by')->nullable();
                $table->string('condition_at_scan', 50)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('session_id')->references('id')->on('library_stock_verification_sessions')->cascadeOnDelete();
                $table->foreign('book_copy_id')->references('id')->on('book_copies')->cascadeOnDelete();
                $table->foreign('scanned_shelf_id')->references('id')->on('library_shelves')->nullOnDelete();
                $table->foreign('expected_shelf_id')->references('id')->on('library_shelves')->nullOnDelete();
                $table->foreign('scanned_by')->references('id')->on('users')->nullOnDelete();
                $table->unique(['session_id', 'book_copy_id']);
            });
        }

        // 22. Library Audit Logs
        if (!Schema::hasTable('library_audit_logs')) {
            Schema::create('library_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 100); // BOOK_ISSUED, BOOK_RETURNED, FINE_WAIVED, MANUAL_OVERRIDE, etc.
                $table->string('entity_type', 100);
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->string('ip_address', 50)->nullable();
                $table->text('user_agent')->nullable();
                $table->jsonb('payload_before')->nullable();
                $table->jsonb('payload_after')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                $table->index(['entity_type', 'entity_id']);
                $table->index(['action']);
            });
        }

        // 23. Extend book_reservations
        if (Schema::hasTable('book_reservations')) {
            Schema::table('book_reservations', function (Blueprint $table) {
                if (!Schema::hasColumn('book_reservations', 'copy_id')) {
                    $table->unsignedBigInteger('copy_id')->nullable()->after('book_id');
                    $table->foreign('copy_id')->references('id')->on('book_copies')->nullOnDelete();
                }
                if (!Schema::hasColumn('book_reservations', 'notification_sent_at')) {
                    $table->timestamp('notification_sent_at')->nullable()->after('expires_at');
                }
                if (!Schema::hasColumn('book_reservations', 'cancellation_reason')) {
                    $table->string('cancellation_reason')->nullable()->after('notification_sent_at');
                }
            });
        }

        // 24. Extend library_settings
        if (Schema::hasTable('library_settings')) {
            Schema::table('library_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('library_settings', 'grace_period_days')) {
                    $table->unsignedInteger('grace_period_days')->default(0)->after('loan_days');
                }
                if (!Schema::hasColumn('library_settings', 'max_fine_limit')) {
                    $table->decimal('max_fine_limit', 8, 2)->default(500.00)->after('fine_per_day');
                }
                if (!Schema::hasColumn('library_settings', 'lost_book_fine_type')) {
                    $table->string('lost_book_fine_type', 50)->default('fixed_multiplier')->after('max_fine_limit');
                }
                if (!Schema::hasColumn('library_settings', 'lost_book_multiplier')) {
                    $table->decimal('lost_book_multiplier', 4, 2)->default(1.50)->after('lost_book_fine_type');
                }
                if (!Schema::hasColumn('library_settings', 'allow_holiday_fine')) {
                    $table->boolean('allow_holiday_fine')->default(false)->after('lost_book_multiplier');
                }
                if (!Schema::hasColumn('library_settings', 'reservation_expiry_hours')) {
                    $table->unsignedInteger('reservation_expiry_hours')->default(48)->after('allow_holiday_fine');
                }
            });
        }

        // 25. Extend employees table with library suspension flags
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'library_suspended')) {
                    $table->boolean('library_suspended')->default(false);
                }
                if (!Schema::hasColumn('employees', 'library_suspension_reason')) {
                    $table->string('library_suspension_reason')->nullable();
                }
            });
        }

        // 26. Seed Default Standard Book Categories if empty
        if (DB::table('book_categories')->count() === 0) {
            $categories = [
                ['name' => 'Academic Textbooks', 'code' => 'CAT-ACAD', 'description' => 'Syllabus and curriculum textbooks for classes 1-12'],
                ['name' => 'Reference Books', 'code' => 'CAT-REF', 'description' => 'Encyclopaedias, dictionaries, and reference handbooks'],
                ['name' => 'General Knowledge', 'code' => 'CAT-GK', 'description' => 'Current affairs, world records, trivia, and quizzes'],
                ['name' => 'Fiction', 'code' => 'CAT-FIC', 'description' => 'Novels, drama, sci-fi, and creative literary fiction'],
                ['name' => 'Non-Fiction', 'code' => 'CAT-NFIC', 'description' => 'Biographies, autobiographies, essays, and memoirs'],
                ['name' => 'Children\'s Books', 'code' => 'CAT-KIDS', 'description' => 'Early reader illustrated stories, picture books, and rhymes'],
                ['name' => 'Competitive Examination', 'code' => 'CAT-EXAM', 'description' => 'JEE, NEET, NTSE, Olympiad, and scholarship preparations'],
                ['name' => 'Dictionaries & Thesaurus', 'code' => 'CAT-DICT', 'description' => 'Language dictionaries and bilingual lexical references'],
                ['name' => 'Encyclopaedias', 'code' => 'CAT-ENCY', 'description' => 'Comprehensive multi-volume general and thematic encyclopaedias'],
                ['name' => 'Religious & Cultural', 'code' => 'CAT-CULT', 'description' => 'Indian heritage, mythology, spiritual texts, and cultural history'],
                ['name' => 'Storybooks', 'code' => 'CAT-STRY', 'description' => 'Fables, Panchatantra, Jataka tales, and folk narratives'],
                ['name' => 'Science & Technology', 'code' => 'CAT-SCIT', 'description' => 'Applied science, astronomy, computer science, and robotics'],
                ['name' => 'Language & Literature', 'code' => 'CAT-LANG', 'description' => 'Grammar, poetry, prose, Tamil, English, and Hindi literature'],
                ['name' => 'History & Geography', 'code' => 'CAT-HIST', 'description' => 'World history, Indian independence, cartography, and atlas'],
                ['name' => 'Mathematics', 'code' => 'CAT-MATH', 'description' => 'Pure mathematics, statistics, calculus, and mathematical puzzles'],
                ['name' => 'Other / Miscellaneous', 'code' => 'CAT-MISC', 'description' => 'General unclassified library resources'],
            ];
            foreach ($categories as $cat) {
                DB::table('book_categories')->insert([
                    'name' => $cat['name'],
                    'code' => $cat['code'],
                    'description' => $cat['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 27. Migrate Existing Book Copy Records
        $existingBooks = DB::table('books')->get();
        foreach ($existingBooks as $book) {
            $existingCopiesCount = DB::table('book_copies')->where('book_id', $book->id)->count();
            if ($existingCopiesCount === 0) {
                $totalCopies = max(1, (int) $book->total_copies);
                for ($i = 1; $i <= $totalCopies; $i++) {
                    $suffix = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $acc = $book->accession_number ? ($book->accession_number . '-' . $suffix) : ('ACC-' . $book->id . '-' . $suffix);
                    $barcode = 'BC-' . str_replace('-', '', $acc);

                    DB::table('book_copies')->insert([
                        'book_id' => $book->id,
                        'accession_number' => $acc,
                        'barcode' => $barcode,
                        'copy_number' => $i,
                        'condition' => 'good',
                        'status' => 'available',
                        'is_reference_only' => false,
                        'cost_price' => $book->purchase_price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('library_stock_verification_items');
        Schema::dropIfExists('library_stock_verification_sessions');
        Schema::dropIfExists('library_disposal_records');
        Schema::dropIfExists('library_damage_reports');
        Schema::dropIfExists('library_asset_maintenance');
        Schema::dropIfExists('library_assets');
        Schema::dropIfExists('library_periodical_receipts');
        Schema::dropIfExists('library_subscriptions');
        Schema::dropIfExists('library_periodicals');
        Schema::dropIfExists('library_fine_payments');
        Schema::dropIfExists('library_goods_receipt_items');
        Schema::dropIfExists('library_goods_receipts');
        Schema::dropIfExists('book_copies');
        Schema::dropIfExists('library_purchase_order_items');
        Schema::dropIfExists('library_purchase_orders');
        Schema::dropIfExists('library_suppliers');
        Schema::dropIfExists('library_shelves');
        Schema::dropIfExists('library_racks');
        Schema::dropIfExists('library_audit_logs');
    }
};


@extends('layouts.app')
@section('title', 'Circulation Desk — Issue & Return')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Circulation Desk</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">High-Speed Barcode Issuance, Returns & Overdue Clearance</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('library.currently-issued') }}" class="btn btn-secondary btn-sm">Currently Issued</a>
      <a href="{{ route('library.overdue-report') }}" class="btn btn-secondary btn-sm">Overdue Report</a>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    </div>
  @endif

  @if($errors->any())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
      <div class="flex items-start gap-2">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
          <strong class="font-semibold">Circulation Warning:</strong>
          <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-red-700">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  @endif

  {{-- Circulation Tabs (Alpine) --}}
  <div x-data="{ activeTab: 'issue' }" class="space-y-4">
    <div class="flex border-b border-slate-200 gap-4">
      <button type="button" @click="activeTab = 'issue'" 
              :class="activeTab === 'issue' ? 'border-primary-600 text-primary-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
              class="pb-3 border-b-2 text-sm font-medium transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Issue Book (Loan)
      </button>

      <button type="button" @click="activeTab = 'return'" 
              :class="activeTab === 'return' ? 'border-primary-600 text-primary-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
              class="pb-3 border-b-2 text-sm font-medium transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0l-6-6m6 6H3"/></svg>
        Return Book (Check-in & Inspection)
      </button>
    </div>

    {{-- TAB 1: ISSUE BOOK --}}
    <div x-show="activeTab === 'issue'" x-transition class="space-y-5">
      <form method="POST" action="{{ route('library.issue.store') }}" class="card p-6 space-y-5"
            x-data="{
              memberType: 'student',
              barcodeInput: '{{ request('barcode') }}',
              scannedBook: null,
              selectedBorrower: null,
              isOverride: false,
              studentLoanDays: {{ $studentLoanDays }},
              staffLoanDays: {{ $staffLoanDays }},
              get computedDueDate() {
                const days = this.memberType === 'staff' ? this.staffLoanDays : this.studentLoanDays;
                const d = new Date(); d.setDate(d.getDate() + days);
                return d.toISOString().split('T')[0];
              }
            }">
        @csrf

        {{-- Borrower Selection --}}
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">1. Select Patron / Borrower</label>
          <div class="flex items-center gap-4 mb-3">
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
              <input type="radio" name="member_type" value="student" x-model="memberType" class="text-primary-600">
              Student Patron
            </label>
            <label class="flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
              <input type="radio" name="member_type" value="staff" x-model="memberType" class="text-primary-600">
              Teaching / Non-Teaching Staff
            </label>
          </div>

          {{-- Student Dropdown --}}
          <div x-show="memberType === 'student'">
            <select name="student_id" id="student-select" class="input w-full text-sm">
              <option value="">Search or select student...</option>
              @foreach($students as $st)
              <option value="{{ $st->id }}" data-adm="{{ $st->admission_number ?? $st->admission_no }}">
                {{ $st->full_name }} · Admission: {{ $st->admission_number ?? $st->admission_no }} · Class {{ ($st->currentEnrollment?->class?->name ?? '') . ($st->currentEnrollment?->section?->name ? ' (' . $st->currentEnrollment->section->name . ')' : '') ?: 'N/A' }}
              </option>
              @endforeach
            </select>
          </div>

          {{-- Staff Dropdown --}}
          <div x-show="memberType === 'staff'" style="display: none;">
            <select name="employee_id" id="employee-select" class="input w-full text-sm">
              <option value="">Search or select staff member...</option>
              @foreach($employees as $emp)
              <option value="{{ $emp->id }}" data-empno="{{ $emp->employee_code ?? $emp->employee_id ?? $emp->id }}">
                {{ $emp->first_name }} {{ $emp->last_name }} · ID: {{ $emp->employee_code ?? $emp->employee_id ?? $emp->id }} · {{ is_object($emp->designation) ? ($emp->designation->name ?? 'Staff') : ($emp->designation ?: 'Staff') }}
              </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Book / Barcode Scan --}}
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">2. Book Scan / Selection</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2">
            <div>
              <label class="block text-xs text-slate-500 mb-1">Scan Physical Copy Barcode or Accession #</label>
              <input type="text" name="barcode" x-model="barcodeInput" 
                     placeholder="e.g. BC-ACC178953510501" 
                     class="input w-full font-mono text-sm border-primary-300 focus:border-primary-500">
            </div>

            <div>
              <label class="block text-xs text-slate-500 mb-1">Or Select Book Title from Catalogue</label>
              <select name="book_id" id="book-select" class="input w-full text-sm">
                <option value="">Select Book Title</option>
                @foreach($books as $bk)
                <option value="{{ $bk->id }}" data-accession="{{ $bk->accession_number }}">
                  {{ $bk->title }} ({{ $bk->available_copies }} available) · {{ $bk->accession_number }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Due Date & Remarks --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Due Date *</label>
            <input type="date" name="due_date" :value="computedDueDate" class="input w-full text-sm" required>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Circulation Remarks</label>
            <input type="text" name="notes" placeholder="Optional notes or remarks" class="input w-full text-sm">
          </div>
        </div>

        {{-- Manager Override Option --}}
        <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-lg space-y-2">
          <label class="flex items-center gap-2 text-xs font-semibold text-amber-900 cursor-pointer">
            <input type="checkbox" name="is_override" value="1" x-model="isOverride" class="w-4 h-4 rounded text-amber-600">
            Authorized Manager Override (Bypass borrowing quota or overdue limits)
          </label>
          <div x-show="isOverride" style="display: none;" class="pt-2">
            <label class="block text-[11px] font-semibold text-amber-800 mb-1">Mandatory Override Reason *</label>
            <input type="text" name="override_reason" placeholder="e.g., Principal approved exam preparation reference extension" class="input w-full text-xs">
          </div>
        </div>

        <div class="pt-2">
          <button type="submit" class="btn btn-primary w-full py-2.5 text-sm font-semibold flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Confirm & Issue Book Copy
          </button>
        </div>
      </form>
    </div>

    {{-- TAB 2: RETURN BOOK --}}
    <div x-show="activeTab === 'return'" style="display: none;" class="space-y-5">
      <form method="POST" action="{{ route('library.return') }}" class="card p-6 space-y-5"
            x-data="{
              condition: 'good',
              damageFine: 0
            }">
        @csrf

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">1. Select Issued Book to Check-In</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2">
            <div>
              <label class="block text-xs text-slate-500 mb-1">Scan Copy Barcode to Return</label>
              <input type="text" name="barcode" placeholder="Scan barcode..." class="input w-full font-mono text-sm">
            </div>

            <div>
              <label class="block text-xs text-slate-500 mb-1">Or Select from Active Issued Loans</label>
              <select name="issue_id" class="input w-full text-sm">
                <option value="">Select Active Issue Record</option>
                @foreach($activeIssues as $ai)
                <option value="{{ $ai->id }}">
                  #{{ $ai->id }} · {{ $ai->book?->title }} ({{ $ai->copy?->accession_number ?: $ai->book?->accession_number }}) · {{ $ai->borrowerName() }} · Due {{ $ai->due_date?->format('d M') }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Condition Inspection --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">2. Physical Condition Inspection</label>
          
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block text-xs text-slate-600 mb-1">Inspected Condition *</label>
              <select name="condition" x-model="condition" class="input w-full text-sm">
                <option value="good">Good Condition (No damage)</option>
                <option value="minor_damage">Minor Damage (Normal wear)</option>
                <option value="major_damage">Major Damage (Requires repair)</option>
                <option value="torn_pages">Torn Pages</option>
                <option value="water_damage">Water Damaged</option>
                <option value="lost">Lost by Borrower</option>
              </select>
            </div>

            <div>
              <label class="block text-xs text-slate-600 mb-1">Return Date</label>
              <input type="date" name="return_date" value="{{ date('Y-m-d') }}" class="input w-full text-sm">
            </div>

            <div x-show="condition !== 'good' && condition !== 'minor_damage'">
              <label class="block text-xs font-semibold text-red-700 mb-1">Damage / Loss Fine (₹)</label>
              <input type="number" step="0.01" name="damage_fine" placeholder="₹ Amount" class="input w-full text-sm border-red-300">
            </div>
          </div>

          <div>
            <label class="block text-xs text-slate-600 mb-1">Inspection Notes / Remarks</label>
            <input type="text" name="inspection_notes" placeholder="Condition details or missing items noted upon return" class="input w-full text-sm">
          </div>
        </div>

        <div class="pt-2">
          <button type="submit" class="btn btn-primary w-full py-2.5 text-sm font-semibold flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Confirm Return & Calculate Dues
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Currently Active Loans Register --}}
  <div class="card overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h3 class="font-bold text-slate-900 text-sm">Recent Active Loans Desk</h3>
        <p class="text-xs text-slate-500">Live borrowings pending return</p>
      </div>
      <a href="{{ route('library.currently-issued') }}" class="btn btn-secondary btn-xs text-xs">View All Active ({{ $activeIssues->count() }})</a>
    </div>

    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Receipt #</th>
            <th class="py-3 px-4">Book Title</th>
            <th class="py-3 px-4">Accession</th>
            <th class="py-3 px-4">Borrower</th>
            <th class="py-3 px-4">Issue Date</th>
            <th class="py-3 px-4">Due Date</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($activeIssues as $issue)
          <tr class="hover:bg-slate-50/80 transition">
            <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $issue->issue_receipt_no ?: ('#' . $issue->id) }}</td>
            <td class="py-3 px-4 font-semibold text-slate-800 text-xs max-w-xs truncate">{{ $issue->book?->title }}</td>
            <td class="py-3 px-4 font-mono text-xs text-primary-600">{{ $issue->copy?->accession_number ?: $issue->book?->accession_number }}</td>
            <td class="py-3 px-4 text-xs font-medium text-slate-800">
              {{ $issue->borrowerName() }}
              <span class="text-[10px] text-slate-400 block font-normal">{{ $issue->borrowerIdentifier() }}</span>
            </td>
            <td class="py-3 px-4 text-xs text-slate-500">{{ $issue->issue_date?->format('d M Y') }}</td>
            <td class="py-3 px-4 text-xs font-semibold {{ $issue->days_overdue > 0 ? 'text-red-600' : 'text-slate-700' }}">
              {{ $issue->due_date?->format('d M Y') }}
              @if($issue->days_overdue > 0)
              <span class="text-[10px] text-red-500 block font-normal">{{ $issue->days_overdue }} days overdue</span>
              @endif
            </td>
            <td class="py-3 px-4">
              <span class="badge {{ $issue->days_overdue > 0 ? 'badge-red' : 'badge-blue' }} text-xs">
                {{ $issue->days_overdue > 0 ? 'Overdue' : 'Issued' }}
              </span>
            </td>
            <td class="py-3 px-4 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-1.5">
                <form action="{{ route('library.return') }}" method="POST" class="inline">
                  @csrf
                  <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                  <button type="submit" class="btn btn-secondary btn-xs text-xs">Return</button>
                </form>

                <form action="{{ route('library.issue.renew', $issue->id) }}" method="POST" class="inline">
                  @csrf
                  <button type="submit" class="btn btn-secondary btn-xs text-xs" title="Extend loan">Renew</button>
                </form>

                <a href="{{ route('library.issue.receipt', $issue->id) }}" target="_blank" class="btn btn-secondary btn-xs text-xs">Receipt</a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-8 text-slate-400 text-xs">No active books currently on loan.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

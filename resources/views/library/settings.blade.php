@extends('layouts.app')
@section('title', 'Library Policy Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Library Configuration & Rules</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Circulation Quotas, Fine Calculation Rates, Grace Periods & Reservation Rules</p>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
      <ul class="list-disc list-inside space-y-0.5 text-xs">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('library.settings.save') }}" class="space-y-6">
    @csrf

    @foreach(['student' => $student, 'employee' => $staff] as $type => $setting)
    <div class="card p-6 space-y-4 border-l-4 {{ $type === 'student' ? 'border-l-primary-600' : 'border-l-indigo-600' }}">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h2 class="font-bold text-slate-900 text-base capitalize flex items-center gap-2">
          @if($type === 'student')
            🎓 Student Circulation Policy
          @else
            👔 Staff & Faculty Circulation Policy
          @endif
        </h2>
        <span class="text-xs text-slate-400">Rules applied at issue & check-in</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Max Books Allowed *</label>
          <input type="number" name="types[{{ $type }}][max_books]" min="1" max="20"
                 value="{{ $setting->max_books }}" class="input w-full text-sm" required>
          <span class="text-[10px] text-slate-400">Simultaneous active loans</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Loan Period (Days) *</label>
          <input type="number" name="types[{{ $type }}][loan_days]" min="1" max="180"
                 value="{{ $setting->loan_days }}" class="input w-full text-sm" required>
          <span class="text-[10px] text-slate-400">Standard borrowing window</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Max Renewals *</label>
          <input type="number" name="types[{{ $type }}][max_renewals]" min="0" max="10"
                 value="{{ $setting->max_renewals }}" class="input w-full text-sm" required>
          <span class="text-[10px] text-slate-400">Times a loan can be extended</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Grace Period (Days)</label>
          <input type="number" name="types[{{ $type }}][grace_period_days]" min="0" max="30"
                 value="{{ $setting->grace_period_days ?? 0 }}" class="input w-full text-sm">
          <span class="text-[10px] text-slate-400">Days before fines begin</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Overdue Fine / Day (₹) *</label>
          <input type="number" step="0.5" name="types[{{ $type }}][fine_per_day]" min="0"
                 value="{{ $setting->fine_per_day }}" class="input w-full text-sm" required>
          <span class="text-[10px] text-slate-400">Daily late fee</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Maximum Fine Cap (₹)</label>
          <input type="number" step="10" name="types[{{ $type }}][max_fine_limit]" min="0"
                 value="{{ $setting->max_fine_limit ?? 500 }}" class="input w-full text-sm">
          <span class="text-[10px] text-slate-400">Upper cap per overdue book</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Lost Book Multiplier</label>
          <input type="number" step="0.1" name="types[{{ $type }}][lost_book_multiplier]" min="1" max="5"
                 value="{{ $setting->lost_book_multiplier ?? 1.5 }}" class="input w-full text-sm">
          <span class="text-[10px] text-slate-400">e.g. 1.5x of book price</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Holding Expiry (Hours)</label>
          <input type="number" name="types[{{ $type }}][reservation_expiry_hours]" min="12" max="168"
                 value="{{ $setting->reservation_expiry_hours ?? 48 }}" class="input w-full text-sm">
          <span class="text-[10px] text-slate-400">Reserved copy claim window</span>
        </div>
      </div>

      <div class="pt-2">
        <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
          <input type="checkbox" name="types[{{ $type }}][allow_holiday_fine]" value="1"
                 @checked($setting->allow_holiday_fine) class="w-4 h-4 rounded text-primary-600">
          Charge fines during official school holidays & calendar breaks
        </label>
      </div>
    </div>
    @endforeach

    <div class="flex justify-end">
      <button type="submit" class="btn btn-primary py-2.5 px-6 font-semibold text-sm">
        Save & Apply Configuration
      </button>
    </div>
  </form>

</div>
@endsection

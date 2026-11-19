@extends('layouts.app')

@section('title', 'Transfer Certificate — ' . $student->full_name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ route('students.show', $student->id) }}" class="btn-icon">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <h1 class="page-title">Transfer Certificate</h1>
    </div>
    <button onclick="window.print()" class="btn btn-primary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Print TC
    </button>
  </div>

  <div id="tc-print" class="card p-8 print:shadow-none print:border-none">
    <div class="border-b-2 border-[#8C2826] pb-5 mb-6 flex items-center justify-between gap-4">
      <div class="flex items-center gap-4 text-left">
        <div class="w-16 h-16 shrink-0">
          <img src="{{ asset('images/school-logo.png') }}" alt="Erode Public School" class="w-full h-full object-contain" onerror="this.src='{{ asset('images/school-crest-transparent.png') }}'">
        </div>
        <div>
          <h2 class="text-xl font-black text-[#8C2826] uppercase tracking-tight leading-tight">ERODE PUBLIC SCHOOL</h2>
          <p class="text-xs font-bold text-slate-700">Senior Secondary &bull; Affiliated to CBSE, New Delhi (Affil No: 1931585)</p>
          <p class="text-[11px] text-slate-500 mt-0.5">No. 420, Vakkil Thottam, Manickampalayam, Erode - 638 004 &bull; Ph: +91 93677 54654</p>
        </div>
      </div>
      <div class="text-right">
        <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-[#8C2826] text-white">
          TRANSFER CERTIFICATE
        </span>
      </div>
    </div>

    <table class="w-full text-sm border-collapse">
      @php
        $rows = [
          ['Sr. No.', 'TC-' . date('Y') . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT)],
          ['Admission No.', $student->admission_number],
          ['Student\'s Name', strtoupper($student->full_name)],
          ['Father\'s Name', strtoupper($student->father_name)],
          ['Mother\'s Name', strtoupper($student->mother_name)],
          ['Date of Birth', $student->dob?->format('d M Y')],
          ['Class Last Studied', $student->currentEnrollment?->class?->name ?? '—'],
          ['Date of Admission', $student->admission_date?->format('d M Y')],
          ['Date of Leaving', now()->format('d M Y')],
          ['Reason for Leaving', 'Parent\'s Request'],
          ['Conduct', 'Good'],
          ['Fee Dues', 'NIL'],
        ];
      @endphp
      @foreach($rows as [$label, $value])
        <tr class="border-b border-slate-200">
          <td class="py-2.5 pr-4 font-semibold text-slate-600 w-2/5">{{ $label }}</td>
          <td class="py-2.5 text-slate-800">{{ $value }}</td>
        </tr>
      @endforeach
    </table>

    <div class="mt-10 grid grid-cols-3 text-center text-sm text-slate-600">
      <div><div class="border-t border-slate-400 pt-2 mt-8">Class Teacher</div></div>
      <div><div class="border-t border-slate-400 pt-2 mt-8">Accountant</div></div>
      <div><div class="border-t border-slate-400 pt-2 mt-8">Principal</div></div>
    </div>
  </div>

</div>
@endsection

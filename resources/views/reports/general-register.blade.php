@extends('layouts.app')
@section('title', 'General Register')
@section('content')
<div class="space-y-6">

  {{-- Interactive Print Controls --}}
  <x-print.controls title="General Register" defaultSize="a4" defaultOrientation="landscape" :backUrl="route('dashboard')" />

  {{-- Print Watermark & Centralized Institutional Letterhead --}}
  <div class="print-only">
    <x-print.watermark />
    <x-print.header 
      title="GENERAL ADMISSION REGISTER" 
      subtitle="Comprehensive Pupil Enrollment & Demographic Register" 
      :academicYear="now()->year . '-' . (now()->year + 1)" 
    />
  </div>

  <div class="no-print flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">General Register</h1>
    <div class="flex gap-2">
      <a href="{{ route('dashboard') }}" class="btn-sm btn-secondary">← Dashboard</a>
      <button onclick="window.print()" class="btn-sm btn-primary flex items-center gap-1 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Register
      </button>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" class="no-print card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select">
        <option value="">All Classes</option>
        @foreach($classes as $cls)
          <option value="{{ $cls->id }}" @selected(request('class_id')==$cls->id)>{{ $cls->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Gender</label>
      <select name="gender" class="select">
        <option value="">All</option>
        <option value="male" @selected(request('gender')=='male')>Male</option>
        <option value="female" @selected(request('gender')=='female')>Female</option>
        <option value="other" @selected(request('gender')=='other')>Other</option>
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('reports.general-register') }}" class="btn-sm btn-secondary">Reset</a>
    <span class="text-sm text-slate-500 ml-auto self-center">Total: <strong>{{ $students->total() }}</strong> students</span>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm border-collapse">
      <thead>
        <tr class="bg-slate-100 dark:bg-slate-800 text-left">
          <th class="th whitespace-nowrap">#</th>
          <th class="th whitespace-nowrap">Adm. No.</th>
          <th class="th">Name</th>
          <th class="th whitespace-nowrap">Class</th>
          <th class="th whitespace-nowrap">DOB</th>
          <th class="th">Gender</th>
          <th class="th whitespace-nowrap">Blood</th>
          <th class="th">Father / Guardian</th>
          <th class="th whitespace-nowrap">Phone</th>
          <th class="th">Religion</th>
          <th class="th whitespace-nowrap">Admission Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $i => $s)
        <tr class="tr hover:bg-slate-50">
          <td class="td text-slate-400 whitespace-nowrap">{{ $students->firstItem() + $i }}</td>
          <td class="td font-mono text-xs font-bold whitespace-nowrap">{{ $s->admission_no ?? $s->admission_number ?? '—' }}</td>
          <td class="td font-medium text-slate-900">{{ $s->first_name }} {{ $s->last_name }}</td>
          <td class="td whitespace-nowrap">{{ $s->class_name ?? '—' }}</td>
          <td class="td whitespace-nowrap">{{ $s->dob ? \Carbon\Carbon::parse($s->dob)->format('d/m/Y') : '—' }}</td>
          <td class="td capitalize">{{ $s->gender }}</td>
          <td class="td whitespace-nowrap">{{ $s->blood_group ?? '—' }}</td>
          <td class="td">{{ $s->father_name ?? '—' }}</td>
          <td class="td whitespace-nowrap">{{ $s->phone ?? '—' }}</td>
          <td class="td">{{ $s->religion ?? '—' }}</td>
          <td class="td whitespace-nowrap">{{ $s->admission_date ? \Carbon\Carbon::parse($s->admission_date)->format('d/m/Y') : '—' }}</td>
        </tr>
        @empty
        <tr><td class="td text-slate-400 text-center" colspan="11">No students found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Print Signatures & Footer --}}
  <div class="print-only mt-8 pt-4">
    <x-print.signatures mode="academic" />
    <x-print.footer />
  </div>

  <div class="no-print mt-4">{{ $students->withQueryString()->links() }}</div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Fine / Late Fee Collection Report')
@section('content')
<div class="space-y-6">

    {{-- Interactive Print Controls --}}
    <x-print.controls title="Fine Collection Report" defaultSize="a4" defaultOrientation="landscape" :backUrl="route('fees.index')" />

    {{-- Print Watermark & Centralized Institutional Letterhead --}}
    <div class="print-only">
        <x-print.watermark />
        <x-print.header 
            title="LATE FEE & FINE REALIZATION REGISTER" 
            subtitle="Student Fine Penalties & Realized Dues Audit" 
            :academicYear="now()->year . '-' . (now()->year + 1)" 
        />
    </div>

    <div class="no-print flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Fine Collection Report</h1>
            <p class="page-subtitle">Late fee / fine collected from students</p>
        </div>
        <button onclick="window.print()" class="btn btn-secondary btn-sm cursor-pointer">Print Register</button>
    </div>

    <div class="no-print card">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="label">Class</label>
                <select name="class_id" class="select">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">From Date</label>
                <input type="date" name="from_date" class="input" value="{{ request('from_date') }}">
            </div>
            <div>
                <label class="label">To Date</label>
                <input type="date" name="to_date" class="input" value="{{ request('to_date') }}">
            </div>
            <button type="submit" class="btn btn-primary">Generate</button>
        </form>
    </div>

    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-700">{{ $payments->count() }} transactions</h3>
            <span class="text-lg font-bold text-red-600">Total Fine: ₹{{ number_format($totalFine, 2) }}</span>
        </div>
        <div class="table-wrap">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-800 text-left">
                        <th class="th">Date</th>
                        <th class="th">Receipt No.</th>
                        <th class="th">Student</th>
                        <th class="th">Class</th>
                        <th class="th">Fee Head</th>
                        <th class="th text-right">Amount Paid</th>
                        <th class="th text-right">Late Fee</th>
                        <th class="th">Mode</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="tr hover:bg-slate-50">
                        <td class="td">{{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}</td>
                        <td class="td font-mono text-blue-600 font-bold">{{ $p->receipt_number }}</td>
                        <td class="td">
                            <p class="font-medium text-slate-900">{{ $p->student?->first_name }} {{ $p->student?->last_name }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $p->student?->admission_no ?? $p->student?->admission_number }}</p>
                        </td>
                        <td class="td text-slate-500">{{ $p->student?->currentEnrollment?->class?->name ?? '—' }}</td>
                        <td class="td">{{ $p->feeHead?->name ?? '—' }}</td>
                        <td class="td text-right">₹{{ number_format($p->amount, 2) }}</td>
                        <td class="td text-right font-semibold text-red-600">₹{{ number_format($p->late_fee, 2) }}</td>
                        <td class="td text-slate-500 capitalize">{{ $p->payment_mode }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="td text-center text-slate-400 py-8">No fine transactions found for the selected filters.</td></tr>
                    @endforelse
                </tbody>
                @if($payments->count() > 0)
                <tfoot>
                    <tr class="bg-slate-50 font-bold border-t-2 border-slate-300">
                        <td colspan="6" class="td font-semibold text-right uppercase text-xs">Total Fine Collected:</td>
                        <td class="td font-bold text-red-600 text-right text-base">₹{{ number_format($totalFine, 2) }}</td>
                        <td class="td"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Print Signatures & Footer --}}
    <div class="print-only mt-8 pt-4">
        <x-print.signatures mode="financial" />
        <x-print.footer />
    </div>

</div>
@endsection

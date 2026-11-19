<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Curriculum Syllabus — {{ $class->display_name ?? $class->name }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @page { size: A4 portrait; margin: 0 !important; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; background: #fff; margin: 0; padding: 10mm 12mm 10mm 18mm !important; box-sizing: border-box !important; font-size: 10px; line-height: 1.45; }
    .term-section { margin-bottom: 20px; page-break-inside: avoid; }
    .term-title { font-size: 11px; font-weight: 800; color: #1e293b; background: #f8fafc; border-left: 4px solid #1e293b; padding: 6px 10px; margin-bottom: 10px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .subject-title { font-size: 10.5px; font-weight: 700; color: #0f172a; margin: 12px 0 6px; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; display: flex; align-items: center; justify-content: space-between; }
    table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9.5px; }
    table.data-table th { background: #f1f5f9; color: #334155; text-align: left; padding: 6px 8px; font-weight: bold; border: 1px solid #cbd5e1; text-transform: uppercase; font-size: 8.5px; }
    table.data-table td { padding: 5px 8px; border: 1px solid #cbd5e1; vertical-align: top; }
    table.data-table tr:nth-child(even) { background: #f8fafc; }
    .status-badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
    .status-completed { background: #dcfce7; color: #166534; }
    .status-in_progress { background: #fef3c7; color: #92400e; }
    .status-pending { background: #f1f5f9; color: #64748b; }
    @media print {
      body { padding: 0 !important; background: #fff !important; }
      .no-print { display: none !important; }
      * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
  </style>
</head>
<body>

  {{-- Print Toolbar --}}
  <x-print.controls title="Curriculum Syllabus Plan" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('academics.syllabus')" />

  {{-- Watermark & Centralized Header --}}
  <x-print.watermark />
  <x-print.header 
    title="OFFICIAL ACADEMIC CURRICULUM SYLLABUS" 
    :subtitle="'Grade / Standard: ' . ($class->display_name ?? $class->name)" 
    :academicYear="$currentYear->name ?? (now()->year . '-' . (now()->year + 1))" 
    :date="now()->format('d M Y')"
  />

  @forelse($syllabus as $termName => $subjects)
    <div class="term-section">
      <div class="term-title">📌 {{ strtoupper($termName) }}</div>
      @foreach($subjects as $subjectName => $chapters)
        <div class="subject-title">
          <span>📚 {{ $subjectName }}</span>
          <span style="font-size: 9px; font-weight: normal; color: #64748b;">{{ count($chapters) }} Scheduled Chapters</span>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 8%; text-align:center;">Ch #</th>
              <th style="width: 32%;">Chapter Title</th>
              <th style="width: 38%;">Key Topics &amp; Learning Objectives</th>
              <th style="width: 10%; text-align:center;">Periods</th>
              <th style="width: 12%; text-align:center;">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($chapters as $ch)
              <tr>
                <td style="text-align: center; font-weight: bold; font-family: monospace;">{{ $ch->chapter_number }}</td>
                <td style="font-weight: 600;">{{ $ch->title }}</td>
                <td style="color: #475569;">{{ $ch->description ?: '—' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $ch->estimated_periods ?? '—' }}</td>
                <td style="text-align: center;">
                  <span class="status-badge status-{{ $ch->status ?? 'pending' }}">
                    {{ str_replace('_', ' ', $ch->status ?? 'pending') }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 12px;">No chapters assigned for this subject in {{ $termName }}.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      @endforeach
    </div>
  @empty
    <div style="text-align: center; padding: 40px; color: #94a3b8; border: 1px dashed #cbd5e1; border-radius: 6px; margin: 20px 0;">
      No curriculum syllabus records published for this class yet.
    </div>
  @endforelse

  {{-- Signatures & Verification --}}
  <div style="margin-top: 24px;">
    <x-print.signatures mode="academic" />
  </div>

  <x-print.footer />

</body>
</html>

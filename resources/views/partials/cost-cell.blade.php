@if ($cost['total'] !== null)
    <span class="fw-semibold">{{ number_format($cost['total']) }} {{ $cost['currency'] }}</span>
    @unless ($cost['is_complete'])
        <span class="badge text-bg-warning">تقدير غير مكتمل</span>
    @endunless
@else
    <span class="text-muted">غير متوفر</span>
@endif

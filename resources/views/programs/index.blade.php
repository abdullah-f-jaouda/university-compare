@extends('layouts.app')

@section('title', 'البرامج')

@section('content')
    <h2 class="mb-4">البرامج الأكاديمية</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('programs.index') }}" method="GET" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="q">بحث</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="اسم البرنامج أو المجال أو الجامعة">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="university_id">الجامعة</label>
                    <select id="university_id" name="university_id" class="form-select">
                        <option value="">كل الجامعات</option>
                        @foreach ($universities as $university)
                            <option value="{{ $university->id }}" @selected(request('university_id') == $university->id)>{{ $university->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label" for="discipline">المجال</label>
                    <select id="discipline" name="discipline" class="form-select">
                        <option value="">كل المجالات</option>
                        @foreach ($disciplines as $discipline)
                            <option value="{{ $discipline }}" @selected(request('discipline') === $discipline)>{{ $discipline }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label" for="degree_type">الدرجة</label>
                    <select id="degree_type" name="degree_type" class="form-select">
                        <option value="">كل الدرجات</option>
                        <option value="diploma" @selected(request('degree_type') === 'diploma')>دبلوم</option>
                        <option value="bachelor" @selected(request('degree_type') === 'bachelor')>بكالوريوس</option>
                        <option value="master" @selected(request('degree_type') === 'master')>ماجستير</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label" for="sort">الترتيب</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="name" @selected(request('sort', 'name') === 'name')>الاسم</option>
                        <option value="cost" @selected(request('sort') === 'cost')>التكلفة (الأقل أولًا)</option>
                        <option value="duration" @selected(request('sort') === 'duration')>المدة (الأقصر أولًا)</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="max_duration">أقصى مدة (سنوات)</label>
                    <input type="number" step="0.5" min="0" max="10" id="max_duration" name="max_duration" value="{{ request('max_duration') }}" class="form-control">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="max_cost">أقصى تكلفة إجمالية تقديرية (LYD)</label>
                    <input type="number" step="100" min="0" id="max_cost" name="max_cost" value="{{ request('max_cost') }}" class="form-control">
                    <div class="form-text">عند تحديد حد للتكلفة تُستبعد البرامج ذات الرسوم الناقصة أو غير المحسوبة، حتى لا يظهر إجماليها الجزئي كأنه الأرخص.</div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">تطبيق</button>
                <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary">إعادة ضبط</a>
            </div>
        </div>
    </form>

    <p class="text-muted">عدد النتائج: {{ $programs->count() }}</p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-fit mb-0">
                <thead class="table-light">
                    <tr>
                        <th>البرنامج</th>
                        <th>الجامعة</th>
                        <th>الدرجة</th>
                        <th>المدة</th>
                        <th>التكلفة التقديرية</th>
                        <th>حالة البيانات</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $program)
                        <tr>
                            <td>{{ $program->name }}</td>
                            <td>{{ $program->university->name }}</td>
                            <td>{{ \App\Support\Labels::degree($program->degree_type) }}</td>
                            <td>{{ \App\Support\Labels::orNA($program->duration_years !== null ? (float) $program->duration_years : null, ' سنة') }}</td>
                            <td>@include('partials.cost-cell', ['cost' => $program->cost])</td>
                            <td>@include('partials.data-status', ['status' => $program->data_status])</td>
                            <td><a href="{{ route('programs.show', $program) }}" class="btn btn-sm btn-outline-primary">التفاصيل</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">لا توجد برامج مطابقة للبحث.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

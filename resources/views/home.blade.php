@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <div class="p-5 mb-4 bg-white rounded-3 shadow-sm text-center">
        <h1 class="fw-bold">قارن بين الجامعات والبرامج في بنغازي</h1>
        <p class="lead text-muted">
            دليل منظّم للجامعات الخاصة، مع تفصيل الرسوم وتقدير التكلفة الإجمالية لكل برنامج.
        </p>

        <form action="{{ route('programs.index') }}" method="GET" class="row g-2 justify-content-center mt-4">
            <div class="col-12 col-md-6">
                <input type="text" name="q" class="form-control form-control-lg" placeholder="ابحث عن تخصص أو جامعة، مثال: هندسة البرمجيات">
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary btn-lg w-100">بحث</button>
            </div>
        </form>
    </div>

    <div class="row g-3 text-center">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="display-6 fw-bold text-primary">{{ $universitiesCount }}</div>
                <div class="text-muted">جامعة</div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="display-6 fw-bold text-primary">{{ $programsCount }}</div>
                <div class="text-muted">برنامج</div>
            </div></div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card shadow-sm h-100"><div class="card-body d-flex flex-column justify-content-center gap-2">
                <a href="{{ route('universities.index') }}" class="btn btn-outline-primary">تصفّح الجامعات</a>
                <a href="{{ route('programs.index') }}" class="btn btn-outline-primary">تصفّح كل البرامج</a>
            </div></div>
        </div>
    </div>
@endsection

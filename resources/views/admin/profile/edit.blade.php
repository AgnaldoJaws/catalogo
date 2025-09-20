@extends('admin.layout')
@section('title','Perfil do negócio')

@section('content')
    <form method="post" action="{{ route('admin.profile.update',['business'=>$biz->id]) }}" class="card">
        @csrf
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input name="name" class="form-control" value="{{ old('name',$biz->name) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">WhatsApp (com DDI/DD)</label>
                <input name="whatsapp" class="form-control" value="{{ old('whatsapp',$biz->whatsapp) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Sobre</label>
                <textarea name="about" class="form-control" rows="4">{{ old('about',$biz->about) }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Instagram</label>
                    <input name="instagram" class="form-control" value="{{ old('instagram',$biz->instagram) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Facebook</label>
                    <input name="facebook" class="form-control" value="{{ old('facebook',$biz->facebook) }}">
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button class="btn btn-primary">Salvar</button>
        </div>
    </form>
@endsection

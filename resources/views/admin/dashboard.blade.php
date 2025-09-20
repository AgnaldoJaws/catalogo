@extends('admin.layout')

@section('title','Dashboard')
@section('content')
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="fw-bold mb-1">Perfil</div>
                    <a href="{{ route('admin.profile.edit',['business'=>$biz->id]) }}" class="btn btn-sm btn-primary">Editar perfil</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="fw-bold mb-1">Locais</div>
                    <a href="{{ route('admin.locations.index',['business'=>$biz->id]) }}" class="btn btn-sm btn-primary">Gerenciar locais</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="fw-bold mb-1">Cardápio</div>
                    <a href="{{ route('admin.menu.sections.index',['business'=>$biz->id]) }}" class="btn btn-sm btn-primary">Seções e itens</a>
                </div>
            </div>
        </div>
    </div>
@endsection

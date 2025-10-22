@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <section aria-labelledby="atalhos-titulo">
        <h1 id="atalhos-titulo" class="visually-hidden">Atalhos do painel administrativo</h1>

        <div class="row g-4 justify-content-center">

            {{-- PERFIL --}}
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.profile.edit') }}"
                   class="big-tile text-decoration-none"
                   aria-label="Editar perfil da empresa">
                    <i class="bi bi-person-circle" aria-hidden="true"></i>
                    <span class="title">Perfil</span>
                    <span class="desc">Nome, descrição, redes e logomarca.</span>
                </a>
            </div>

            {{-- LOCAIS --}}
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.locations.index') }}"
                   class="big-tile text-decoration-none"
                   aria-label="Gerenciar locais de atendimento">
                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    <span class="title">Locais</span>
                    <span class="desc">Endereços, WhatsApp e horários.</span>
                </a>
            </div>

            {{-- CARDÁPIO --}}
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.menu.sections.index') }}"
                   class="big-tile text-decoration-none"
                   aria-label="Editar cardápio">
                    <i class="bi bi-list-ul" aria-hidden="true"></i>
                    <span class="title">Cardápio</span>
                    <span class="desc">Seções, itens, preços e fotos.</span>
                </a>
            </div>

        </div>
    </section>

    <style>
        /* ========================
           DASHBOARD ESTILO
           ======================== */
        :root {
            font-size: 18px;
        }

        /* Cards grandes */
        .big-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            height: 220px;
            border-radius: 18px;
            border: 2px solid #e5e7eb;
            background: #fff;
            text-align: center;
            padding: 1rem;
            transition: all .15s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,.03);
        }

        .big-tile:hover,
        .big-tile:focus-visible {
            border-color: #fe3d28;
            box-shadow: 0 6px 20px rgba(0,0,0,.08);
            transform: translateY(-2px);
            text-decoration: none;
            outline: none;
        }

        .big-tile i {
            font-size: 2.6rem;
            color: #fe3d28;
        }

        .big-tile .title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }

        .big-tile .desc {
            font-size: 1rem;
            color: #4b5563;
            line-height: 1.3;
        }

        /* Mobile adjustments */
        @media (max-width: 767.98px) {
            .big-tile {
                height: 180px;
                border-radius: 16px;
                font-size: 1rem;
            }

            .big-tile i {
                font-size: 2.2rem;
            }

            .big-tile .desc {
                font-size: .95rem;
            }
        }
    </style>
@endsection

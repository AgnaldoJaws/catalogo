@extends('layouts.app')

@section('title','Catálogo de Comida — Sua Cidade')

@section('content')
    @include('home.components.hero-and-filters')
    @include('home.components.status-bar')
    @include('home.components.card-grid')
@endsection

@push('scripts')
    <script>
        (function($){
            let nearMe = {{ request('sort') === 'nearest' && request('lat') && request('lng') ? 'true' : 'false' }};
            let myPos = (nearMe ? {lat: parseFloat('{{ request('lat') }}'), lng: parseFloat('{{ request('lng') }}')} : null);
            const initialCat = '{{ request('category_slug') ?? (($categories[0]['slug'] ?? '')) }}';
            let activeCategory = initialCat;

            const $cards = $('.card-restaurant');
            const $selectCity = $('#selectCity');
            const $selectSort = $('#selectSort');
            const $radius = $('#radiusRange');
            const $cityLabel = $('#cityLabel');
            const $cityCurrent = $('#cityCurrent');
            const $resultCount = $('#resultCount');
            const $queryLabel = $('#queryLabel');

            if (activeCategory) {
                $('#catScroller .chip').each(function(){
                    const ok = $(this).data('cat') === activeCategory;
                    $(this).attr('aria-selected', ok ? 'true' : 'false');
                });
            }

            function toRad(d){ return d*Math.PI/180; }
            function distKm(a,b){
                if(!a||!b||isNaN(a.lat)||isNaN(a.lng)||isNaN(b.lat)||isNaN(b.lng)) return null;
                const R=6371, dLat=toRad(b.lat-a.lat), dLng=toRad(b.lng-a.lng);
                const s = Math.sin(dLat/2)**2 + Math.cos(toRad(a.lat))*Math.cos(toRad(b.lat))*Math.sin(dLng/2)**2;
                return 2*R*Math.asin(Math.sqrt(s));
            }

            function updateDistances(){
                $cards.each(function(){
                    const $c=$(this);
                    const lat=parseFloat($c.data('lat')), lng=parseFloat($c.data('lng'));
                    const $badge=$c.find('.badge-distance');
                    if(nearMe && myPos && isFinite(lat) && isFinite(lng)){
                        const d=distKm(myPos,{lat,lng});
                        if(d!=null){ $badge.text(d.toFixed(1).replace('.',',')+' km').show(); }
                    } else { $badge.hide(); }
                });
            }

            function applyFilters(){
                const city = ($selectCity.val() || '').trim();
                const radiusKm = parseInt($radius.val(),10) || 5;
                let visible=0;

                $cards.each(function(){
                    const $c=$(this);
                    const cardCity = ($c.data('city')||'').trim();
                    const cats = String($c.data('cats')||'').toLowerCase();
                    let ok = (city==='' || cardCity.toLowerCase()===city.toLowerCase());
                    if(ok && activeCategory){ ok = cats.includes(String(activeCategory).toLowerCase()); }
                    if(ok && nearMe && myPos){
                        const d = distKm(myPos,{lat:parseFloat($c.data('lat')),lng:parseFloat($c.data('lng'))});
                        ok = (d!=null && d<=radiusKm);
                    }
                    $c.closest('.col')[ ok ? 'show' : 'hide' ]();
                    if(ok) visible++;
                });

                if ($selectSort.val()==='nearest' && nearMe && myPos){
                    const $row = $cards.first().closest('.row');
                    const cols = $row.children('.col:visible').get();
                    cols.sort((a,b)=>{
                        const ca=$(a).find('.card-restaurant'), cb=$(b).find('.card-restaurant');
                        const da=distKm(myPos,{lat:parseFloat(ca.data('lat')),lng:parseFloat(ca.data('lng'))})||9999;
                        const db=distKm(myPos,{lat:parseFloat(cb.data('lat')),lng:parseFloat(cb.data('lng'))})||9999;
                        return da-db;
                    });
                    cols.forEach(el=>$row.append(el));
                }

                $resultCount.text(visible);
                const chipText = $('#catScroller .chip[aria-selected="true"]').text().trim() || 'tudo';
                $queryLabel.text('“'+chipText+'”');
                if (city){
                    const txt = $('#selectCity option:selected').text();
                    $cityLabel.text(txt); $cityCurrent.text(txt);
                }
            }

            // chips → atualiza category_slug e submete pro backend
            $('#catScroller').on('click', '.chip', function(){
                $('#catScroller .chip[aria-selected="true"]').attr('aria-selected','false');
                $(this).attr('aria-selected','true');
                activeCategory = $(this).data('cat');
                $('#category_slug').val(activeCategory);
                document.getElementById('filtersForm').submit();
            });

            $selectCity.on('change', function(){ applyFilters(); }); // submit já acontece pelo onchange
            $selectSort.on('change', function(){
                if ($(this).val()==='nearest' && !(myPos && nearMe)) {
                    $('#btnNearMe').trigger('click');
                } else {
                    document.getElementById('filtersForm').submit();
                }
            });

            $('#btnNearMe').on('click', function(e){
                e.preventDefault();
                if (!navigator.geolocation){ alert('Geolocalização não suportada.'); return; }
                navigator.geolocation.getCurrentPosition(
                    (pos)=>{
                        nearMe=true;
                        myPos={lat:pos.coords.latitude,lng:pos.coords.longitude};
                        $('#lat').val(myPos.lat.toFixed(6));
                        $('#lng').val(myPos.lng.toFixed(6));
                        $('#selectSort').val('nearest');
                        $('#radiusWrapper').closest('.col-12').addClass('radius-active');
                        updateDistances();
                        document.getElementById('filtersForm').submit();
                    },
                    (err)=>{ alert('Não foi possível obter sua localização.'); console.warn(err); },
                    { enableHighAccuracy:true, timeout:8000, maximumAge:0 }
                );
            });

            $('#radiusRange').on('input change', function(){
                $('#radiusValue').text(this.value);
                $('#radius_km').val(this.value);
                if(nearMe){ updateDistances(); document.getElementById('filtersForm').submit(); }
            });

            $('#btnClear').on('click', function(){ nearMe=false; myPos=null; });

            if (nearMe) { $('#radiusWrapper').closest('.col-12').addClass('radius-active'); }
            updateDistances(); applyFilters();
        })(jQuery);
    </script>
@endpush

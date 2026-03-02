@extends('layouts.app')

@section('title', 'Nova narudzbenica')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Nova narudzbenica</h1>
</div>

<form action="{{ route('narudzbenice.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Podaci o narudzbenici</div>
                <div class="card-body">
                    @if($apoteke->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label">Apoteka *</label>
                        <select class="form-select" name="apoteka_id" id="apotekaSelect" required>
                            <option value="">Izaberite apoteku...</option>
                            @foreach($apoteke as $apoteka)
                            <option value="{{ $apoteka->id }}">{{ $apoteka->naziv }} - {{ $apoteka->grad }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Dobavljac *</label>
                        <select class="form-select" name="dobavljac_id" id="dobavljacSelect" required>
                            <option value="">Izaberite dobavljaca...</option>
                            @foreach($dobavljaci as $dobavljac)
                            <option value="{{ $dobavljac->id }}" {{ ($preselectedDobavljacId ?? null) == $dobavljac->id ? 'selected' : '' }}>{{ $dobavljac->naziv }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Napomena</label>
                        <textarea class="form-control" name="napomena" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Stavke narudzbenice</div>
                <div class="card-body">
                    <div id="stavkeContainer">
                        <div class="row mb-3 stavka-row">
                            <div class="col-md-5">
                                <select class="form-select lek-select" name="stavke[0][lek_id]" required>
                                    <option value="">Izaberite lek...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="stavke[0][kolicina]" placeholder="Kol." min="1" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" class="form-control cena-input" name="stavke[0][cena_po_komadu]" placeholder="Cena" min="0" required readonly>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger remove-stavka" disabled><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary" id="addStavka">
                        <i class="bi bi-plus-lg"></i> Dodaj stavku
                    </button>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-lg"></i> Kreiraj narudzbenicu</button>
    <a href="{{ route('narudzbenice.index') }}" class="btn btn-secondary btn-lg">Odustani</a>
</form>
@endsection

@push('scripts')
<script>
let stavkaIndex = 1;
const preselectedLekId = {{ ($preselectedLek->id ?? 'null') }};
let previousDobavljacId = document.getElementById('dobavljacSelect').value;

function loadLekovi(dobavljacId, callback) {
    if (!dobavljacId) {
        document.querySelectorAll('.lek-select').forEach(select => {
            select.innerHTML = '<option value="">Izaberite lek...</option>';
        });
        return;
    }

    fetch(`/narudzbenice/dobavljac/${dobavljacId}/lekovi`)
        .then(r => r.json())
        .then(data => {
            document.querySelectorAll('.lek-select').forEach(select => {
                select.innerHTML = '<option value="">Izaberite lek...</option>';
                data.forEach(item => {
                    select.innerHTML += `<option value="${item.lek_id}" data-cena="${item.nabavna_cena}">${item.lek.naziv} - ${item.nabavna_cena} RSD</option>`;
                });
            });
            if (callback) callback();
        });
}

function hasFilledStavke() {
    let filled = false;
    document.querySelectorAll('.stavka-row').forEach(row => {
        const lek = row.querySelector('.lek-select').value;
        const kol = row.querySelector('[name*="kolicina"]').value;
        const cena = row.querySelector('[name*="cena_po_komadu"]').value;
        if (lek || kol || cena) filled = true;
    });
    return filled;
}

function resetAllStavke() {
    // Remove extra rows, keep only the first
    const container = document.getElementById('stavkeContainer');
    const rows = container.querySelectorAll('.stavka-row');
    rows.forEach((row, i) => { if (i > 0) row.remove(); });

    // Clear first row
    const firstRow = container.querySelector('.stavka-row');
    firstRow.querySelector('.lek-select').value = '';
    firstRow.querySelector('[name*="kolicina"]').value = '';
    firstRow.querySelector('.cena-input').value = '';
    firstRow.querySelector('.remove-stavka').disabled = true;
    stavkaIndex = 1;
}

document.getElementById('dobavljacSelect').addEventListener('change', function() {
    if (hasFilledStavke()) {
        if (!confirm('Promena dobavljaca ce obrisati sve unete stavke jer novi dobavljac mozda nema iste lekove. Da li zelite da nastavite?')) {
            this.value = previousDobavljacId;
            return;
        }
        resetAllStavke();
    }
    previousDobavljacId = this.value;
    loadLekovi(this.value);
});

// Auto-load lekovi if supplier is preselected
const dobavljacSelect = document.getElementById('dobavljacSelect');
if (dobavljacSelect.value) {
    loadLekovi(dobavljacSelect.value, function() {
        if (preselectedLekId) {
            const lekSelect = document.querySelector('.lek-select');
            if (lekSelect) {
                lekSelect.value = preselectedLekId;
                const selectedOption = lekSelect.selectedOptions[0];
                if (selectedOption && selectedOption.dataset.cena) {
                    const cenaInput = lekSelect.closest('.stavka-row').querySelector('[name*="cena_po_komadu"]');
                    if (cenaInput) cenaInput.value = selectedOption.dataset.cena;
                }
            }
        }
    });
}

document.getElementById('addStavka').addEventListener('click', function() {
    const container = document.getElementById('stavkeContainer');
    const row = container.querySelector('.stavka-row').cloneNode(true);
    row.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[0\]/, `[${stavkaIndex}]`);
        if (el.tagName !== 'SELECT') el.value = '';
    });
    row.querySelector('.remove-stavka').disabled = false;
    container.appendChild(row);
    stavkaIndex++;
    updateRemoveButtons();
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.stavka-row');
    rows.forEach(row => {
        row.querySelector('.remove-stavka').disabled = rows.length === 1;
    });
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-stavka')) {
        e.target.closest('.stavka-row').remove();
        updateRemoveButtons();
    }
});

// Auto-fill cena when lek is selected
document.addEventListener('change', function(e) {
    if (e.target.matches('.lek-select')) {
        const row = e.target.closest('.stavka-row');
        const cenaInput = row.querySelector('.cena-input');
        const selectedOption = e.target.selectedOptions[0];
        if (e.target.value && selectedOption && selectedOption.dataset.cena) {
            cenaInput.value = selectedOption.dataset.cena;
        } else {
            cenaInput.value = '';
        }
    }
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Nova prodaja')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Nova prodaja</h1>
</div>

<form action="{{ route('prodaje.store') }}" method="POST" id="prodajaForm">
    @csrf

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-cart"></i> Stavke prodaje
                </div>
                <div class="card-body">
                    <div id="stavkeContainer">
                        <div class="stavka-row row mb-3" data-index="0">
                            <div class="col-md-6">
                                <label class="form-label">Lek</label>
                                <select class="form-select lek-select" name="stavke[0][lek_id]" required>
                                    <option value="">Izaberite lek...</option>
                                    @foreach($zalihe as $zaliha)
                                    <option value="{{ $zaliha->lek_id }}"
                                            data-cena="{{ $zaliha->prodajna_cena }}"
                                            data-kolicina="{{ $zaliha->kolicina }}"
                                            data-naziv="{{ $zaliha->lek->naziv }}"
                                            data-na-recept="{{ $zaliha->lek->na_recept ? '1' : '0' }}">
                                        {{ $zaliha->lek->naziv }} ({{ $zaliha->lek->jkl_sifra }})
                                        @if($zaliha->lek->na_recept) [Rx] @endif -
                                        Dostupno: {{ $zaliha->kolicina }} | Cena: {{ number_format($zaliha->prodajna_cena, 2) }} RSD
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Kolicina</label>
                                <input type="number" class="form-control kolicina-input" name="stavke[0][kolicina]"
                                       min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Popust</label>
                                <input type="number" class="form-control popust-input" name="stavke[0][popust]"
                                       min="0" step="0.01" value="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger remove-stavka" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary" id="addStavka">
                        <i class="bi bi-plus-lg"></i> Dodaj stavku
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4" id="receptCard">
                <div class="card-header" id="receptHeader">
                    <i class="bi bi-file-medical"></i> Recept <span id="receptRequired" class="badge bg-danger d-none">OBAVEZNO</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Broj recepta *</label>
                        <input type="text" class="form-control" id="brojRecepta" placeholder="Unesite broj recepta"
                               value="{{ $recept ? $recept->broj_recepta : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">JMBG pacijenta *</label>
                        <input type="text" class="form-control" id="jmbgPacijenta" placeholder="13 cifara" maxlength="13" pattern="[0-9]{13}"
                               value="{{ $recept ? $recept->jmbg_pacijenta : '' }}">
                        <small class="text-muted">Obavezno za verifikaciju recepta</small>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mb-3" id="validateRecept">
                        <i class="bi bi-search"></i> Proveri recept
                    </button>
                    <input type="hidden" name="recept_id" id="receptId">
                    <div id="receptInfo"></div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-credit-card"></i> Placanje
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nacin placanja</label>
                        <select class="form-select" name="nacin_placanja" required>
                            @foreach($nacinPlacanja as $nacin)
                            <option value="{{ $nacin->value }}">{{ $nacin->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Ukupno:</span>
                        <strong id="ukupnoIznos">0.00 RSD</strong>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100">
                <i class="bi bi-check-lg"></i> Zavrsi prodaju
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let stavkaIndex = 1;
let receptLekovi = []; // Čuva info o lekovima sa recepta (lek_id, naziv, preostalo)

document.getElementById('addStavka').addEventListener('click', function() {
    const container = document.getElementById('stavkeContainer');
    const firstRow = container.querySelector('.stavka-row');
    const newRow = firstRow.cloneNode(true);

    newRow.dataset.index = stavkaIndex;

    newRow.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[0\]/, `[${stavkaIndex}]`);
        if (el.type !== 'hidden') el.value = el.type === 'number' ? (el.classList.contains('kolicina-input') ? 1 : 0) : '';
    });

    newRow.querySelector('.remove-stavka').disabled = false;
    container.appendChild(newRow);

    stavkaIndex++;
    updateRemoveButtons();
    calculateTotal();
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-stavka')) {
        e.target.closest('.stavka-row').remove();
        updateRemoveButtons();
        calculateTotal();
        checkPrescriptionRequired();
    }
});

document.addEventListener('change', function(e) {
    if (e.target.matches('.lek-select, .kolicina-input, .popust-input')) {
        calculateTotal();
        checkPrescriptionRequired();
        validatePrescriptionQuantities();
    }
});

// Validacija količina na receptu u realnom vremenu
function validatePrescriptionQuantities() {
    if (!document.getElementById('receptId').value || receptLekovi.length === 0) {
        return;
    }

    document.querySelectorAll('.stavka-row').forEach(row => {
        const select = row.querySelector('.lek-select');
        const kolicinaInput = row.querySelector('.kolicina-input');

        if (select.value && select.selectedOptions[0].dataset.naRecept === '1') {
            const lekId = parseInt(select.value);
            const kolicina = parseInt(kolicinaInput.value) || 0;

            const receptLek = receptLekovi.find(l => l.lek_id === lekId);

            if (receptLek && kolicina > receptLek.preostalo) {
                kolicinaInput.classList.add('is-invalid');
                // Dodaj tooltip ako ne postoji
                if (!kolicinaInput.nextElementSibling || !kolicinaInput.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = `Maksimalno: ${receptLek.preostalo} kom`;
                    kolicinaInput.parentNode.appendChild(feedback);
                }
            } else {
                kolicinaInput.classList.remove('is-invalid');
                const feedback = kolicinaInput.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            }
        } else {
            kolicinaInput.classList.remove('is-invalid');
            const feedback = kolicinaInput.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        }
    });
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.stavka-row');
    rows.forEach((row, index) => {
        row.querySelector('.remove-stavka').disabled = rows.length === 1;
    });
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.stavka-row').forEach(row => {
        const select = row.querySelector('.lek-select');
        const kolicina = parseInt(row.querySelector('.kolicina-input').value) || 0;
        const popust = parseFloat(row.querySelector('.popust-input').value) || 0;

        if (select.value) {
            const cena = parseFloat(select.selectedOptions[0].dataset.cena) || 0;
            total += (kolicina * cena) - popust;
        }
    });
    document.getElementById('ukupnoIznos').textContent = total.toFixed(2) + ' RSD';
}

function checkPrescriptionRequired(preserveServerMessage = false) {
    let requiresPrescription = false;
    let prescriptionMedicines = [];

    document.querySelectorAll('.stavka-row').forEach(row => {
        const select = row.querySelector('.lek-select');
        if (select.value && select.selectedOptions[0].dataset.naRecept === '1') {
            requiresPrescription = true;
            prescriptionMedicines.push(select.selectedOptions[0].dataset.naziv);
        }
    });

    const receptCard = document.getElementById('receptCard');
    const receptRequired = document.getElementById('receptRequired');
    const receptInfo = document.getElementById('receptInfo');

    // Proveri da li postoji poruka od servera (alert-danger ili alert-success)
    const hasServerMessage = receptInfo.querySelector('.alert-danger, .alert-success');

    if (requiresPrescription) {
        receptCard.classList.add('border-danger');
        receptRequired.classList.remove('d-none');

        // Prikaži upozorenje samo ako nema unetog recepta i nema poruke od servera
        if (!document.getElementById('receptId').value && !hasServerMessage && !preserveServerMessage) {
            const uniqueMeds = [...new Set(prescriptionMedicines)];
            receptInfo.innerHTML =
                `<div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Potreban recept!</strong><br>
                    Sledeći lekovi se izdaju na recept: <strong>${uniqueMeds.join(', ')}</strong>
                </div>`;
        }
    } else {
        receptCard.classList.remove('border-danger');
        receptRequired.classList.add('d-none');
        if (!document.getElementById('receptId').value && !hasServerMessage) {
            receptInfo.innerHTML = '';
        }
    }
}

// Validacija forme pre slanja
document.getElementById('prodajaForm').addEventListener('submit', function(e) {
    let requiresPrescription = false;
    let prescriptionMedicines = [];

    document.querySelectorAll('.stavka-row').forEach(row => {
        const select = row.querySelector('.lek-select');
        if (select.value && select.selectedOptions[0].dataset.naRecept === '1') {
            requiresPrescription = true;
            prescriptionMedicines.push(select.selectedOptions[0].dataset.naziv);
        }
    });

    // Provera da li je potreban recept
    if (requiresPrescription && !document.getElementById('receptId').value) {
        e.preventDefault();
        const uniqueMeds = [...new Set(prescriptionMedicines)];
        alert('Greška: Sledeći lekovi zahtevaju validan recept: ' + uniqueMeds.join(', ') + '\n\nMolimo unesite broj recepta i JMBG pacijenta, zatim kliknite "Proveri recept".');

        document.getElementById('brojRecepta').focus();
        document.getElementById('receptCard').scrollIntoView({ behavior: 'smooth' });
        return;
    }

    // Provera količina na receptu (ako postoji recept)
    if (document.getElementById('receptId').value && receptLekovi.length > 0) {
        let quantityErrors = [];

        document.querySelectorAll('.stavka-row').forEach(row => {
            const select = row.querySelector('.lek-select');
            const kolicinaInput = row.querySelector('.kolicina-input');

            if (select.value && select.selectedOptions[0].dataset.naRecept === '1') {
                const lekId = parseInt(select.value);
                const kolicina = parseInt(kolicinaInput.value) || 0;
                const nazivLeka = select.selectedOptions[0].dataset.naziv;

                // Pronađi lek na receptu
                const receptLek = receptLekovi.find(l => l.lek_id === lekId);

                if (receptLek) {
                    if (kolicina > receptLek.preostalo) {
                        quantityErrors.push(
                            `• ${nazivLeka}: tražite ${kolicina} kom, preostalo na receptu: ${receptLek.preostalo} kom`
                        );
                        // Označi polje crveno
                        kolicinaInput.classList.add('is-invalid');
                    } else {
                        kolicinaInput.classList.remove('is-invalid');
                    }
                } else {
                    // Lek nije na receptu
                    quantityErrors.push(
                        `• ${nazivLeka}: ovaj lek nije na validiranom receptu`
                    );
                }
            }
        });

        if (quantityErrors.length > 0) {
            e.preventDefault();
            alert('Greška u količinama:\n\n' + quantityErrors.join('\n') + '\n\nMolimo smanjite količinu i pokušajte ponovo.');
            return;
        }
    }
});

// Omogući unos samo brojeva u JMBG polje
document.getElementById('jmbgPacijenta').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13);
});

// Resetuj validaciju recepta ako se promeni broj recepta ili JMBG
document.getElementById('brojRecepta').addEventListener('input', function() {
    if (document.getElementById('receptId').value) {
        document.getElementById('receptId').value = '';
        receptLekovi = [];
        document.getElementById('receptInfo').innerHTML = '';
        checkPrescriptionRequired();
        // Ukloni sve invalid oznake
        document.querySelectorAll('.kolicina-input.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            const feedback = el.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        });
    }
});

document.getElementById('jmbgPacijenta').addEventListener('change', function() {
    if (document.getElementById('receptId').value) {
        document.getElementById('receptId').value = '';
        receptLekovi = [];
        document.getElementById('receptInfo').innerHTML = '';
        checkPrescriptionRequired();
    }
});

// Validacija količina dok korisnik kuca
document.addEventListener('input', function(e) {
    if (e.target.matches('.kolicina-input')) {
        validatePrescriptionQuantities();
    }
});

document.getElementById('validateRecept').addEventListener('click', function() {
    const brojRecepta = document.getElementById('brojRecepta').value.trim();
    const jmbg = document.getElementById('jmbgPacijenta').value.trim();

    if (!brojRecepta) {
        document.getElementById('receptInfo').innerHTML =
            `<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Unesite broj recepta.</div>`;
        return;
    }

    if (!jmbg || jmbg.length !== 13) {
        document.getElementById('receptInfo').innerHTML =
            `<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Unesite ispravan JMBG (13 cifara).</div>`;
        return;
    }

    // Prikazi loading
    document.getElementById('receptInfo').innerHTML =
        `<div class="text-center"><span class="spinner-border spinner-border-sm"></span> Provera...</div>`;

    fetch('{{ route('prodaje.validate-recept') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            broj_recepta: brojRecepta,
            jmbg: jmbg
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('receptInfo').innerHTML =
                `<div class="alert alert-danger"><i class="bi bi-x-circle"></i> ${data.error}</div>`;
            document.getElementById('receptId').value = '';
            receptLekovi = [];
            checkPrescriptionRequired(true);
        } else {
            document.getElementById('receptId').value = data.recept.id;
            receptLekovi = data.lekovi; // Sačuvaj info o lekovima za validaciju količina

            // Prikaži informacije o receptu i preostalim količinama
            let lekoviHtml = data.lekovi.map(l => {
                let statusClass = l.preostalo === l.propisano ? 'text-success' : 'text-warning';
                return `<tr>
                    <td>${l.naziv}</td>
                    <td class="text-center">${l.propisano}</td>
                    <td class="text-center">${l.izdato}</td>
                    <td class="text-center fw-bold ${statusClass}">${l.preostalo}</td>
                </tr>`;
            }).join('');

            document.getElementById('receptInfo').innerHTML =
                `<div class="alert alert-success mb-2">
                    <i class="bi bi-check-circle"></i> <strong>Recept validan</strong><br>
                    <small>Pacijent: ${data.recept.pacijent}</small><br>
                    <small>Važi do: ${data.recept.datum_vazenja}</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lek</th>
                                <th class="text-center" style="width:60px">Prop.</th>
                                <th class="text-center" style="width:60px">Izd.</th>
                                <th class="text-center" style="width:60px">Preost.</th>
                            </tr>
                        </thead>
                        <tbody>${lekoviHtml}</tbody>
                    </table>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="bi bi-info-circle"></i> Možete izdati samo preostalu količinu leka.
                </small>`;

            document.getElementById('receptCard').classList.remove('border-danger');
            document.getElementById('receptRequired').classList.add('d-none');
        }
    })
    .catch(error => {
        document.getElementById('receptInfo').innerHTML =
            `<div class="alert alert-danger"><i class="bi bi-x-circle"></i> Greška pri komunikaciji sa serverom.</div>`;
    });
});

// Inicijalna provera na učitavanju stranice
checkPrescriptionRequired();

// Ako je recept proslijedjen kroz URL, automatski pokreni validaciju
@if($recept)
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('validateRecept').click();
});
@endif
</script>
@endpush

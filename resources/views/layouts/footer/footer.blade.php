
<!-- Bootstrap5 js cdn -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

<!-- bootstrap5 dataTables js cdn -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        $('.alert').alert();

        $('select').each(function () {
            const $select = $(this);

            $select.select2({
                theme: 'bootstrap-5',
                width: $select.data('width') || ($select.hasClass('w-100') ? '100%' : 'style'),
                placeholder: $select.data('placeholder'),
            });

            // Select2 emette il suo `change` tramite jQuery. Propaghiamo anche
            // l'evento nativo per i selettori che usano addEventListener('change', ...).
            // Gli onchange inline sono già gestiti da jQuery e non vanno duplicati.
            if (!$select.is('[onchange]')) {
                $select.on('select2:select select2:unselect select2:clear', function () {
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }
        });
    })
</script>

@yield('scriptSrc')

@yield('scripts')

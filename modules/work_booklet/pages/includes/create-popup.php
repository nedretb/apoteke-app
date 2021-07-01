<div class="create-popup">

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?= ___('Kreiranje plana godišnjeg odmora') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Napomena: Prije kreiranja plana, potrebno je provjeriti da li je plan u potpunosti popunjen. Provjeru plana
                    možete uraditi klikom na <a href="/apoteke-app/modules/default/pages/popup_plan_go.php?employee_no=<?= $_user['employee_no'] ?>" target="_blank" class="text-primary"><b>ovaj link</b></a>.
                    <br><br>
                    Ukoliko ste saglasni sa kreiranim planom, molimo da potvrdite tako što ćete
                    kliknuti na dugme "Kreirajte plan".
                </div>
                <div class="modal-footer">
                    <button type="button" class="my-btn my-btn-p" data-dismiss="modal"><?= ___('Zatvorite') ?></button>
                    <a href="?m=work_booklet&p=pregled-planova&kreiraj-plan-go=1">
                        <button type="button" class="my-btn my-btn-b"><?= ___('Kreirajte plan') ?></button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
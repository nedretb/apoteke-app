# TABELE

## Performance management
    
    - ID
    - user_id
    - sent (cannot add any more goals)
    - status (accepted_from_supervisor & accepted_from_employee) // 1 stays for accepted
    - accepted_from_supervisor
    - accepted_from_employee
    - year
    - development_plan
    - category ?? or not
   
Scenario :

1. Korisnik kreira novi sporazum (to samo pod uslovom da nema ni jedan aktivan)
2. Nakon toga, korisnik dodaje ciljeve koje direktno veže za taj sporazum ;; 
       
### Performance management - cijevi

    - id_sporazuma
    - kategorija // FK
    - naziv_cilja
    - opis_cilja
    - kvalitativno
    - kvantitativno
    - tezina
    - naziv_sa_opisom
    - realizacija_cilja
    - ocjena
    - komentar

Svaki od uposlenika dodaje željeni broj ciljeva popunjavajući informacije kao što su :

    - Kategorija cilja (Šifarnik)
    - Naziv cilja
    - Opis cilja
    - Kvalitativno  ?? 
    - Kvantitativno ??
    - Težina ??
    - NAZIV SA OPISOM???
    
NAPOMENA : Prilikom kreiranja novog sporazuma, svakom od korisnika se kreiraju kompetencije (Sve kompetencije). 
Kompetencija su oblika :: 





Nakon odabira željenih ciljeva, korisnik pritiskom na dugme "Pošaljite", pristaje da je saglasan sa željenim izmjenama i šalje prijedlog sporazuma svome direktno nadređenom (ili njegovom impersonatoru). 

Manager (nadređeni ili impersonator) nakon pregleda prijedloga sporazuma (izvršavajući eventualne izmjene) klikom "Prihvatam sporazum", prihvata sporazum i šalje se obavijest korisniku.

Nakon pregleda eventualnih izmjena, korisnik klikom na dugme "Prihvatam sporazum" prihvata sve detalje sporazuma (U ovoj fazi korisnik nema pravo na izmjene, već samo slažem se ili ne).






<?php

use Carbon\Carbon as Carbon;

if(isset($_GET['what'])){
    $what = $_GET['what'];

    if($what == 'stanovanje'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    $let = Stanovanje::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $stanovanje = Stanovanje::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no

                var_dump($request->get());

                Stanovanje::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/stanovanje.php';
    }else if($what == 'kontakt'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    Kontakt::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $kontakt = Kontakt::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                Kontakt::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/kontakt.php';
    }else if($what == 'roditelji'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    $request->otac_datum_rodjenja = Carbon::parse($request->otac_datum_rodjenja)->format('Y-m-d');
                    $request->majka_datum_rodjenja_ = Carbon::parse($request->majka_datum_rodjenja_)->format('Y-m-d');

                    Roditelji::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $roditelj = Roditelji::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                Roditelji::create($request);
            }
        }
        include $root . '/modules/' . $_mod . '/pages/profile-crud/roditelji.php';
    }else if($what == 'porodicno-stanje'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    $request->supruznik_datum_rodjenja = Carbon::parse($request->supruznik_datum_rodjenja)->format('Y-m-d');
                    PorodicnoStanje::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $porodica = PorodicnoStanje::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                PorodicnoStanje::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/porodicno-stanje.php';
    }else if($what == 'rodbina'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    Rodbina::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $rodbina = Rodbina::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                Rodbina::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/rodbina.php';
    }else if($what == 'licni-dokumenti'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    LicniDokumenti::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $dokumenti = LicniDokumenti::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                LicniDokumenti::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/licni-dokumenti.php';
    }else if($what == 'zdravstveno-stanje'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    ZdravstvenoStanje::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $zdravstv = ZdravstvenoStanje::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                ZdravstvenoStanje::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/zdravstveno-stanje.php';
    }else if($what == 'skolovanje'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    Skolovanje::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $skolovanje = Skolovanje::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                Skolovanje::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/skolovanje.php';
    }else if($what == 'solidarnost'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    Solidarnost::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $solidarnost = Solidarnost::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                Solidarnost::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/solidarnost.php';
    }else if($what == 'porez'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    Porez::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $porez = Porez::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                Porez::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/porez.php';
    }else if($what == 'rodjenje'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    $request->datum_rodjena = Carbon::parse($request->datum_rodjena)->format('Y-m-d');

                    Rodjenje::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $rodjenje = Rodjenje::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                Rodjenje::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/rodjenje.php';
    }else if($what == 'podaci-djeca'){
        if(isset($_GET['id'])){
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                try{
                    $request->datum_rodjena = Carbon::parse($request->datum_rodjena)->format('Y-m-d');

                    PodaciDjeca::where('id = '.$_GET['id'])->update($request->get());
                }catch (\Exception $e){}
            }
            $rodjenje = PodaciDjeca::where('id = '.$_GET['id'])->first();
        }else{
            if(isset($request)){
                $request->employee_no = $_user['employee_no']; // ADD Employee no
                PodaciDjeca::create($request);
            }
        }

        include $root . '/modules/' . $_mod . '/pages/profile-crud/podaci-djeca.php';
    }
}


// ------------------------------------------------------------------------------------------------------------------ //


if(isset($request)){

    die();
}
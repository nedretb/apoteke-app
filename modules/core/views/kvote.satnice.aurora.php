<div class="kvote">

    <div class="row">
        <div class="col-md-2 kvote">
            <span class="badge badge-pill badge-secondary"><?php echo $kvote['go-prethodna-godina']['ukupno']; ?></span>
            <span class="badge badge-pill badge-success"><?php echo $kvote['go-prethodna-godina']['slobodno']; ?></span>
            <span class="badge badge-pill badge-danger"><?php echo $kvote['go-prethodna-godina']['iskoristeno']; ?></span>

            <br/>
            <small style="padding-top:10px;">Godišnji odmor prethodna godina</small>
        </div>


        <div class="col-md-2 kvote">
            <span class="badge badge-pill badge-secondary"><?php echo $kvote['go-tekuca-godina']['ukupno']; ?></span>
            <span class="badge badge-pill badge-success"><?php echo $kvote['go-tekuca-godina']['slobodno']; ?></span>
            <span class="badge badge-pill badge-danger"><?php echo $kvote['go-tekuca-godina']['iskoristeno']; ?></span>

            <br/>
            <small style="padding-top:10px;">Godišnji odmor tekuća godina</small>
        </div>

        <div class="col-md-2 kvote">
            <span class="badge badge-pill badge-secondary"><?php echo $kvote['placeni-vjerski']['ukupno']; ?></span>
            <span class="badge badge-pill badge-success"><?php echo $kvote['placeni-vjerski']['slobodno']; ?></span>
            <span class="badge badge-pill badge-danger"><?php echo $kvote['placeni-vjerski']['iskoristeno']; ?></span>

            <br/>
            <small style="padding-top:10px;">Plaćeni vjerski praznici</small>
        </div>

        <div class="col-md-2 kvote">
            <span class="badge badge-pill badge-secondary"><?php echo $kvote['neplaceni-vjerski']['ukupno']; ?></span>
            <span class="badge badge-pill badge-success"><?php echo $kvote['neplaceni-vjerski']['slobodno']; ?></span>
            <span class="badge badge-pill badge-danger"><?php echo $kvote['neplaceni-vjerski']['iskoristeno']; ?></span>

            <br/>
            <small style="padding-top:10px;">Neplaćeni vjerski praznici</small>
        </div>

        <div class="col-md-2 kvote">
            <span class="badge badge-pill badge-secondary"><?php echo $kvote['placena-odsustva']['ukupno']; ?></span>
            <span class="badge badge-pill badge-success"><?php echo $kvote['placena-odsustva']['slobodno']; ?></span>
            <span class="badge badge-pill badge-danger"><?php echo $kvote['placena-odsustva']['iskoristeno']; ?></span>

            <br/>
            <small style="padding-top:10px;">Plaćena odsustva</small>
        </div>
        <div class="col-md-2 kvote">
            <span class="badge badge-pill badge-danger"><?php echo $kvote['ostala-placena']['iskoristeno']; ?></span>

            <br/>
            <small style="padding-top:10px;">Ostala plaćena odsustva</small>
<!--           Todo: Ostala plaćena odsustva --->
        </div>
    </div>

    <br/>
    <span class="badge small badge-pill badge-secondary">Ukupno</span>

    <span class="badge small badge-pill badge-success">Ostalo</span>

    <span class="badge small badge-pill badge-danger">Iskorišteno</span>
</div>

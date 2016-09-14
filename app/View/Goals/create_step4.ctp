<section class="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">

    <h1 class="goals-create-heading">Set a top key result (tKR)</h1>
    <p class="goals-create-description">Set measurable target to achieve your goal.</p>

    <form class="goals-create-input" action="">

<!--        <label class="goals-create-input-label">Key Result name?</label>
        <p class="goals-create-input-label-discription">your top key result is required.</p>
-->
        <label for="">tKR name</label>
        <input class="form-control goals-create-input-form goals-create-input-form-tkr-name" type="text" placeholder="e.g. Increase monthly active users">

<!--            <select class="form-control goals-create-input-form goals-create-input-form-tkr-weight" name="" id="" disabled>
            <option value="5">Highest</option>
        </select>-->
        <label for="">Unit & Range</label>

        <select class="form-control goals-create-input-form goals-create-input-form-tkr-range-unit mod-select" name="" id="">
            <option value="0">%</option>
            <option value="3">¥</option>
            <option value="4">$</option>
            <option value="1">その他の単位</option>
            <option value="2">なし</option>
        </select>
        <div class="goals-create-layout-flex">

            <input class="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text" placeholder="0">
            <span class="goals-create-input-form-tkr-range-symbol">&gt;</span>
            <input class="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text" placeholder="100">
        </div>


        <a href=""><i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="goals-create-interactive-link">Add description</span></a>
        <textarea class="form-control goals-create-input-form tkr-description" name="" id="" cols="30" rows="10"></textarea>

        <a class="goals-create-btn-next btn" href="/goals/approval/list/gucchi">Save and share</a>
        <a class="goals-create-btn-cancel btn" href="/goals/create/step3/gucchi">Back</a>
    </form>

</section>

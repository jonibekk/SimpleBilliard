<section class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">

    <h1 class="goals-create-heading">Choose a goal image</h1>
    <p class="goals-create-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

    <form class="goals-create-input" action="" enctype="multipart/form-data">
        <label class="goals-create-input-image-label">Goal image?</label>
        <img class="goals-create-input-image-preview" src="" alt="">
        <p>This is sample image if you want to upload your original image</p>
        <label for="file_photo">
            Upload a image
            <input class="goals-create-input-image-form" type="file" name="" id="file_photo" style="display: none;">
        </label>

        <label class="goals-create-input-term-label">Term?</label>
        <select class="form-control goals-create-input-term-form" name="" id="">
            <option value="">This Term(Apr 1, 2016 - Sep 30, 2016)</option>
            <option value="">Next Term(Oct 1, 2016 - Mar 31, 2016)</option>
        </select>

        <a href="">View more options</a>

        <label class="goals-create-input-description-label">Description</label>
        <textarea class="form-control goals-create-input-description-form" name="" id="" cols="30" rows="10"></textarea>

        <label class="goals-create-input-due-date-label">End date</label>
        <input class="form-control goals-create-input-due-date-form-year" type="date">

        <label class="goals-create-input-weight-label">Weight</label>
        <select class="form-control goals-create-input-weight-form" name="" id="">
            <option value="0">0 (認定対象外)</option>
            <option value="1">1 (とても低い)</option>
            <option value="2">2</option>
            <option value="3">3 (デフォルト)</option>
            <option value="4">4</option>
            <option value="5">5 (とても高い)</option>
        </select>

        <a class="btn" href="/goals/create/step2">Back</a>
        <a class="btn btn-primary" href="/goals/create/step4">Next <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
    </form>

</section>

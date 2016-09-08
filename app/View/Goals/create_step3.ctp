<section class="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">

    <h1 class="goals-create-heading">Set your goal image</h1>
    <p class="goals-create-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

    <form class="goals-create-input" action="" enctype="multipart/form-data">
        <label class="goals-create-input-label">Goal image?</label>

        <div class="goals-create-input-image-upload">
            <img class="goals-create-input-image-upload-preview" src="" alt="" width="100" height="100">
            <div class="goals-create-input-image-upload-info">
                <p class="goals-create-input-image-upload-info-text">This is sample image if you want to upload your original image</p>
                <label class="goals-create-input-image-upload-info-link" for="file_photo">
                    Upload a image
                    <input class="goals-create-input-image-upload-info-form" type="file" name="" id="file_photo">
                </label>
            </div>
        </div>

        <label class="goals-create-input-label">Term?</label>
        <select class="form-control goals-create-input-form" name="" id="">
            <option value="">This Term(Apr 1, 2016 - Sep 30, 2016)</option>
            <option value="">Next Term(Oct 1, 2016 - Mar 31, 2016)</option>
        </select>

        <a class="goals-create-view-more" href=""><i class="fa fa-eye" aria-hidden="true"></i> View more options</a>

        <label class="goals-create-input-label">Description</label>
        <textarea class="form-control goals-create-input-form" name="" id="" cols="30" rows="10"></textarea>

        <label class="goals-create-input-label">End date</label>
        <input class="form-control goals-create-input-form" type="date">

        <label class="goals-create-input-label">Weight</label>
        <select class="form-control goals-create-input-form" name="" id="">
            <option value="0">0 (認定対象外)</option>
            <option value="1">1 (とても低い)</option>
            <option value="2">2</option>
            <option value="3">3 (デフォルト)</option>
            <option value="4">4</option>
            <option value="5">5 (とても高い)</option>
        </select>

        <a class="goals-create-btn-next btn" href="/goals/create/step4/gucchi">Next →</a>
        <a class="goals-create-btn-cancel btn" href="/goals/create/step2/gucchi">Back</a>
    </form>

</section>

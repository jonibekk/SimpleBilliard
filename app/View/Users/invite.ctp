<?= $this->App->viewStartComment() ?>

<div id="UserInvite">
    <section class="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
        <p class="gl-form-description">招待先のメールアドレスを入力してください</p>
        <form class="">
            <div class="mb_16px">
                <label class="gl-form-label">招待先メールアドレス</label>
                <textarea name="email" class="form-control" rows="6"></textarea>
            </div>
            <div class="btnGroupForForm">
                <button type="submit" class="btnGroupForForm-next">次へ →</button>
                <a class="btnGroupForForm-cancel" href="/">キャンセル</a>
            </div>
        </form>
    </section>
</div>

<?= $this->App->viewEndComment() ?>

<?php echo $this->Html->css('payments.min'); ?>
<section class="panel enter-cc-info">
    <div class="panel-container">
        <form>
        <div class="group">
            <label>
            <span>Name</span>
            <input name="cardholder-name" class="field" placeholder="Jane Doe" />
            </label>
            <label>
            <span>Phone</span>
            <input class="field" placeholder="(123) 456-7890" type="tel" />
            </label>
        </div>
        <div class="group">
            <label>
            <span>Card</span>
            <div id="card-element" class="field"></div>
            </label>
        </div>
        <button type="submit">Pay $25</button>
        <div class="outcome">
            <div class="error" role="alert"></div>
            <div class="success">
            Success! Your Stripe token is <span class="token"></span>
            </div>
        </div>
        </form>
    </div>
</section>
<script src="https://js.stripe.com/v3/"></script>
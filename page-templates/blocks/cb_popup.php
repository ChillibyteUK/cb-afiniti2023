<style>
.popup {
    position: fixed;
    bottom: 1rem;
    right: 0.5rem;
}
.popup__button {
    background-color: var(--col-green-500);
    color: white;
    font-size: 2rem;
    border-radius: 50%;
    display: grid;
    place-content: center;
    aspect-ratio: 1;
    width: 80px;
    transition: all .2s ease-out;
    box-shadow: rgba(0,0,0,.16) 0 3px 6px, rgba(0,0,0,.23) 0 3px 6px;
    cursor: pointer;
}
.popup__button:hover {
    transform: translateY(2px);
    box-shadow: rgba(0,0,0,.12) 0 1px 3px, rgba(0,0,0,.24) 0 1px 2px;
}
.popup__popup {
    display: none;
    width: min(400px,90vw);
    border-radius: 1rem;
    background-color: var(--col-green-500);
    border-radius: 0.5rem;
    padding: 1rem;
    box-shadow: rgba(0,0,0,.16) 0 3px 6px, rgba(0,0,0,.23) 0 3px 6px;
    position: relative;
}
.popup__popup .h3 {
    color: white;
}
.popup__popup #close {
    cursor: pointer;
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}
.popup__popup .gform_wrapper .gform_footer button {
    background-color: var(--col-orange-500) !important;
    width: 100%;
}
.popup__popup .gform_wrapper .gform_footer button:hover {
    background-color: #f6ab6a !important;
}
.popup__popup .gfield_description.validation_message.gfield_validation_message {
    display: none !important;
}
.popup__popup .gform_confirmation_message {
    color: white;
    font-size: 1rem;
}
</style>
<div class="popup">
    <div class="popup__popup" id="popupPopup">
        <div id="close"><i class="fa-regular fa-times"></i></div>
        <div class="h3 fw-bold">Want to speak with a change specialist?</div>
        <p>Let us know how we can help and we’ll be in touch within one working day.</p>
        <?=do_shortcode('[gravityform id="' . get_field('form_id') . '" title="false" ajax="true"]')?>
    </div>
    <div class="popup__button" id="popupButton">
        <i class="fa-solid fa-message"></i>
    </div>
</div>
<script>
document.getElementById('popupButton').addEventListener('click', function() {
  document.getElementById('popupPopup').style.display = 'block'; // Show the popup
  this.style.display = 'none'; // Hide the button that was clicked
});

document.getElementById('close').addEventListener('click', function() {
  document.getElementById('popupPopup').style.display = 'none'; // Hide the popup
  document.getElementById('popupButton').style.display = 'grid'; // Show the button again
});

document.addEventListener('gform_confirmation_loaded', function(event) {
    console.log('confirmation_loaded');
    document.getElementById('popupPopup').style.display = 'block';
}, false);

</script>
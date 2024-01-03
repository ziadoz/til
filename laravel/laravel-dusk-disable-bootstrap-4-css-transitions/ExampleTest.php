<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

class ModalTest extends DuskTestCase
{
    public function test_modal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/modal');

            // Disable modal CSS transitions because they interfere with clicking the close button.
            $browser->script(<<<SCRIPT
                document.head.insertAdjacentHTML('beforeend', '<style>* { transition: none !important; transform: none; }<style>');
            SCRIPT);
            
            // Open the modal.
            $browser->click('a.open-modal');
            
            // Perform some assertions.
            $browser->whenAvailable('div.modal-dialog', function (Browser $browser) use ($position) {
                // Do stuff...
            });
            
            // Close the modal.
            // Without disabling the transitions, this would fail because things occur so quickly.
            $browser->click('div.modal-dialog button.close');
        });
    }
}
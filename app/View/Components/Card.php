<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    /**
     * Determine if the card is collapsed
     *
     * @var boolean
     */
    public $isCollapsed;

    /**
     * Collapse id for reference if $isCollapsed is true
     *
     * @var string
     *
     */
    public $collapseId;

    /**
     * Header background color
     *
     * @var string
     */
    public $headerColor;

    /**
     * Header URL to be redirected when clicked
     *
     * @var string
     */
    public $headerUrl;

    /**
     * Header icon
     *
     * @var string
     */
    public $icon;

    /**
     * Remove the sides padding of card body
     *
     * @var boolean
     */
    public $hasBodyPadding;


    /**
     * Create a new component instance.
     *
     * @param boolean $isCollapsed
     * @param string $collapseId
     * @param string $icon
     * @param string $headerColor
     * @param string $bodyPadding
     *
     * @return void
     */
    public function __construct(
        $isCollapsed = false,
        $collapseId = '',
        $icon = null,
        $headerColor = 'light',
        $headerUrl = '',
        $hasBodyPadding = true
    ) {
        $this->headerColor = $this->getFromMultiples($headerColor);
        $this->headerUrl = $headerUrl;
        $this->icon = $icon;
        $this->hasBodyPadding = $hasBodyPadding;
        $this->isCollapsed = $isCollapsed;
        $this->collapseId = $collapseId;
    }

    /**
     * Retorna um atribute caso seja informado um array com multiplos
     * possíveis atributos
     *
     * @param string | array $attribute
     *
     * @return string $
     */
    private function getFromMultiples($attribute)
    {
        if (is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                if ($value === true) {
                    return $key;
                }
            }

            if (! is_bool($attribute[array_key_last($attribute)])) {
                return $attribute[array_key_last($attribute)];
            }
        }

        return $attribute;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.card');
    }
}

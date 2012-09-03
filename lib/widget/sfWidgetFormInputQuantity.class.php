<?php

/**
 * sfWidgetFormInputQuantity represents an HTML input tag surrounded by + and - buttons.
 *
 * @author     Jarek Rencz <jrencz@polibuda.info>
 */
class sfWidgetFormInputQuantity extends sfWidgetFormInputText
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('type');
    $this->setOption('type', 'text');
  }
  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return "<a class='quantity_increase s16 s16_sort_asc'></a>" . $this->renderTag('input', array_merge(array('type' => $this->getOption('type'), 'name' => $name, 'value' => $value, 'class'=>'sf_widget_form_input_quantity'), $attributes)) . "<a class='quantity_decrease s16 s16_sort_desc'></a>";
  }
  
  public function getJavascripts()
  {                    
    $js = parent::getJavascripts();
    $js[] = 'piQuizPlugin.sfWidgetFormInputQuantity';
    return $js;
  }

  public function getStylesheets()
  { 
    $css = parent::getStylesheets();
    $css['piQuizPlugin.sfWidgetFormInputQuantity'] = 'all';
    return $css;
  }
}

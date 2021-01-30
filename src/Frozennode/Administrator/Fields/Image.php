<?php

namespace Frozennode\Administrator\Fields;

use Frozennode\Administrator\Includes\Multup;
use Illuminate\Support\Facades\Input;

class Image extends File
{
    /**
     * The specific defaults for the image class.
     *
     * @var array
     */
    protected $imageDefaults = array(
        'sizes' => array(),
    );

    /**
     * The specific rules for the image class.
     *
     * @var array
     */
    protected $imageRules = array(
        'sizes' => 'array',
    );

    /**
     * This static function is used to perform the actual upload and resizing using the Multup class.
     *
     * @return array
     */
    public function doUpload()
    {
        // handle other upload ways
        if($this->getOption('other')) {
            $callback = $this->getOption('callback');
            if (empty($callback)) {
                throw new \InvalidArgumentException('callback config must exist in '.$this->getOption('field_name').' field when upload file in other ways. Model:'.$this->config->getOption('call').' model');
            }
            if (is_callable($callback)) {
                $file = Input::file('file');
                return $callback($file);
            }
        }
        // CJ: Create a folder if it doesn't already exist
        if (!file_exists($this->getOption('location'))) {
            mkdir($this->getOption('location'), 0777, true);
        }

        //use the multup library to perform the upload
        $result = Multup::open('file', 'image|max:'.$this->getOption('size_limit') * 1000, $this->getOption('location'),
                                    $this->getOption('naming') === 'random')
            ->sizes($this->getOption('sizes'))
            ->set_length($this->getOption('length'))
            ->upload();

        return $result[0];
    }

    /**
     * Gets all rules.
     *
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();

        return array_merge($rules, $this->imageRules);
    }

    /**
     * Gets all default values.
     *
     * @return array
     */
    public function getDefaults()
    {
        $defaults = parent::getDefaults();

        return array_merge($defaults, $this->imageDefaults);
    }
}

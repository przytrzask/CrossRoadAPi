<?php
namespace Sda\Crossroad\Light;
class LightBuilder
{
    /**
     * @var int
     */
    private $id = 0;
    /**
     * @var string
     */
    private $state = Light::DEFAULT_STATE;
    /**
     * @var LightValidator
     */
    private $validator;

    public function __construct()
    {
        $this->validator = new LightValidator();
    }

    /**
     * @return Light
     */
    public function build()
    {
        return new Light($this->id, $this->state, $this->validator);
    }

    /**
     * @param int $id
     * @return LightBuilder
     */
    public function withId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $state
     * @return LightBuilder
     */
    public function withState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param LightValidator $validator
     * @return LightBuilder
     */
    public function withValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

}
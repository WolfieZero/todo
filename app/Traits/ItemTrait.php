<?php
namespace App\Traits;

trait ItemTrait
{
    /**
     * Returns the single row from the model.
     *
     * @param   integer  $id  ID of the resource requesting
     * @return  array
     */
    public function item($id)
    {
        if (! property_exists($this, 'defaultModel')) {
            abort('500', 'No `$defaultModel` property defined');
        }

        $model = new $this->defaultModel;

        // Join up methods using with
        if (property_exists($this, 'apiWith') && $this->apiWith > 0) {
            foreach ($this->apiWith as $method) {
                $model = $model->with($method);
            }
        }

        $model = $model->findOrFail($id);
        $data  = $model->toArray();

        if (property_exists($this, 'defaultTransformer') && ! empty($this->defaultTransformer)) {
            $data = $this->defaultTransformer->transform($data);
        }

        return $data;
    }
}

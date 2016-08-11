<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Todo;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests\TodoRequest;
use App\Http\Controllers\Controller;

class TodoApiController extends Controller
{
    use Helpers;

    /**
     * Fields and their default values
     *
     * @var  array
     */
    protected $fields = [
        'task'     => null,
        'user_id'  => null,
        'complete' => '',
        'order'    => '0'
    ];

    /**
     * Return the collection.
     *
     * @return  array
     */
    public function collection()
    {
        $user_id = 1;
        $collection = Todo::where('user_id', $user_id)->orderBy('order')->get();
        return $collection->toArray();
    }

    /**
     * Return a single item.
     *
     * @return  array
     */
    public function single($id)
    {
        $user_id = 1;
        $single = Todo::findOrFail($id);
        return $single->toArray();
    }

    /**
     * Create a new todo item.
     *
     * @param   TodoRequest  $request  Posted data
     * @return  array
     */
    public function store(TodoRequest $request)
    {
        $createTodo = [];

        // Update the model values
        foreach ($this->fields as $key => $val) {
            if ($request->{$key}) {
                $createTodo[$key] = $request->{$key};
            }
        }

        $todo = Todo::create($createTodo);
        return $todo->toArray();
    }

    /**
     * Update the specific todo item.
     *
     * @param   TodoRequest  $request  Posted data
     * @param   integer      $id       Todo ID
     * @return  array
     */
    public function update(TodoRequest $request, $id)
    {
        $todo = Todo::findOrFail($id);

        // Update the model values
        foreach ($this->fields as $key => $val) {
            if ($request->{$key}) {
                $todo->{$key} = $request->{$key};
            }
        }

        // Issue with 0 fix
        if ($request->complete === '0') {
            $todo->complete = 0;
        }

        $todo->save();
        return $todo->toArray();
    }

    /**
     * Grab the list and update all the ids with a new orer.
     * @param   Request  $request  Put data
     * @return  array
     */
    public function reorder(Request $request)
    {
        $order = json_decode($request->list);
        $todoTable = with(new Todo)->getTable();

        $sql = '
            UPDATE ' . $todoTable .'
            SET `order` = CASE
        ';

        foreach ($order as $item) {
            $sql .= 'WHEN `id` = ' . (int)$item->id .' THEN ' . (int)$item->order . ' ';
        }

        $sql .= 'WHEN `id` THEN `order` END';

        if (DB::statement($sql)) {
            return [ 'success' => true ];
        };

        return [ 'error' => 'reorder failed' ];
    }

    /**
     * Delete the specific todo item.
     *
     * @param   integer  $id  Todo ID
     * @return  array
     */
    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);

        if ($todo->delete()) {
            return [ 'success' => true ];
        }

        return [ 'error' => 'delete failed' ];
    }

}

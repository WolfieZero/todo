<?php

namespace App\Http\Controllers\Api;

use DB;
use JWTAuth;
 use App\Todo;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests\TodoRequest;
use App\Http\Controllers\Controller;

class TodoApiController extends Controller
{
    use Helpers;

    /**
     * Fields and their default values.
     *
     * @var  array
     */
    protected $fields = [
        'task'     => null,
        'complete' => '',
        'order'    => '0'
    ];

    /**
     * Todo model for user.
     *
     * @var  App\Todo
     */
    private $todo;

    /**
     * User model for authroized user.
     *
     * @var  App\User
     */
    private $user;

    /**
     * Creates todo model for user.
     */
    public function __construct(Request $request)
    {
        if ($request->token) {
            $this->user = JWTAuth::parseToken()->authenticate();
            $this->todo = $this->user->todos();
        }
    }

    /**
     * Return the collection in order of "order".
     *
     * @return  array
     */
    public function collection()
    {
        $collection = $this->todo->orderBy('order')->get();

        if ($collection) {
            return $this->response->array($collection->toArray());
        }

        return $this->response->error('error', 500);
    }

    /**
     * Return a single item.
     *
     * @return  array
     */
    public function single($id)
    {
        $single = $this->todo->find($id);

        if ($single) {
            return $this->response->array($single->toArray());
        }

        return $this->response->error('cannot_find_todo', 500);
    }

    /**
     * Create a new todo item.
     *
     * @param   TodoRequest  $request  Posted data
     * @return  array
     */
    public function store(TodoRequest $request)
    {
        $create = [];

        // Update the model values
        foreach ($this->fields as $key => $val) {
            if ($request->{$key}) {
                $create[$key] = $request->{$key};
            }
        }

        $created = $this->todo->create($create);

        if ($created) {
            return $this->response->array($created->toArray())->setStatusCode(201);
        }

        return $this->response->error('cannot_create_todo', 500);
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
        $todo = $this->todo->find($id);

        if ($todo === null) {
            return $this->response->error('cannot_find_todo', 500);
        }

        foreach ($this->fields as $key => $val) {
            if ($request->{$key}) {
                $todo->{$key} = $request->{$key};
            }
        }

        // Issue "zero" fix
        if ($request->complete === '0') {
            $todo->complete = 0;
        }

        $updated = $todo->save();

        if ($updated) {
            return $this->response->array($todo->toArray())->setStatusCode(201);
        }

        return $this->response->error('cannot_update_todo', 500);
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
        $sqlCases = '';

        foreach ($order as $item) {
            $sqlCases .= 'WHEN `id` = ' . (int)$item->id .' THEN ' . (int)$item->order . ' ';
        }

        $sql = '
            UPDATE ' . $todoTable .'

            SET `order` = CASE
                ' . $sqlCases . '
                WHEN `id` THEN `order`
            END

            WHERE user_id = ' . $this->user->id .'
        ';

        if (DB::statement($sql)) {
            return $this->response->noContent();
        };

        return $this->response->error('cannot_reorder', 500);
    }

    /**
     * Delete the specific todo item.
     *
     * @param   integer  $id  Todo ID
     * @return  array
     */
    public function destroy($id)
    {
        $todo = $this->todo->find($id);

        if (! $todo) {
            return $this->response->error('cannot_find_todo', 500);
        }

        if ($todo->delete()) {
            return $this->response->noContent();
        }

        return $this->response->error('cannot_delete_todo', 500);
    }

}

<?php namespace Blog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Blog\Models\Blog;


class BlogsController extends Controller
{
    /**
     * Blog Model
     * @var Blog\Models\Blog
     */
    protected $game;

    public function __construct(Blog $game) {
        $this->game = $game;
    }

    public function index(JsonResponse $res) {
        $controller = $this;
        $games = $this->game->orderBy('id', 'asc')->get();

        return new JsonResponse([
            'data' => $games->map(function($game) use ($controller) {
                return $controller->serializeBlog($game);
            }),
        ]);
    }

    public function find(JsonResponse $res, $id) {
        $game = $this->game->findOrFail($id);

        return new JsonResponse([
            'data' => $this->serializeBlog($game),
        ]);
    }

    public function store(Request $req, JsonResponse $res) {
        $type = $req->json('data.type');
        $attrs = $req->json('data.attributes');

        $game = $this->game->create($attrs);

        return new JsonResponse([
            'data' => $this->serializeBlog($game),
        ]);
    }

    public function update(Request $req, JsonResponse $res, $id) {
        $type = $req->json('data.type');
        $attrs = $req->json('data.attributes');

        $game = $this->game->find($id);
        $game->fill($attrs);
        $game->save();

        return new JsonResponse([
            'data' => $this->serializeBlog($game),
        ]);
    }

    public function delete(JsonResponse $res, $id) {
        $this->game->destroy($id);

        return new JsonResponse(null, 204);
    }

    protected function serializeBlog($game) {
        return [
            'type' => 'games',
            'id' => (string) $game->id,
            'attributes' => $game->toArray(),
            'relationships' => $game->getJSONRelationshipsArray(),
        ];
    }
}

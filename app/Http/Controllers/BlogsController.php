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
    protected $blog;

    public function __construct(Blog $blog) {
        $this->blog = $blog;
    }

    public function index(JsonResponse $res) {
        $controller = $this;
        $blogs = $this->blog->orderBy('id', 'asc')->get();

        return new JsonResponse([
            'data' => $blogs->map(function($blog) use ($controller) {
                return $controller->serializeBlog($blog);
            }),
        ]);
    }

    public function find(JsonResponse $res, $id) {
        $blog = $this->blog->findOrFail($id);

        return new JsonResponse([
            'data' => $this->serializeBlog($blog),
        ]);
    }

    public function store(Request $req, JsonResponse $res) {
        $type = $req->json('data.type');
        $attrs = $req->json('data.attributes');

        $blog = $this->blog->create($attrs);

        return new JsonResponse([
            'data' => $this->serializeBlog($blog),
        ]);
    }

    public function update(Request $req, JsonResponse $res, $id) {
        $type = $req->json('data.type');
        $attrs = $req->json('data.attributes');

        $blog = $this->blog->find($id);
        $blog->fill($attrs);
        $blog->save();

        return new JsonResponse([
            'data' => $this->serializeBlog($blog),
        ]);
    }

    public function delete(JsonResponse $res, $id) {
        $this->blog->destroy($id);

        return new JsonResponse(null, 204);
    }

    protected function serializeBlog($blog) {
        return [
            'type' => 'blogs',
            'id' => (string) $blog->id,
            'attributes' => $blog->toArray(),
            'relationships' => $blog->getJSONRelationshipsArray(),
        ];
    }
}

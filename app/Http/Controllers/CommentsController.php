<?php namespace Blog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Validator;

use Blog\Models\Comment;


class CommentsController extends Controller
{
    /**
     * Comment Model
     * @var Blog\Models\Comment
     */
    protected $comment;

    protected $rules = [
        'blog' => 'required|exists:blogs,id',
        'username' => 'required|min:2',
        'content' => 'required|min:0',
    ];

    public function __construct(Comment $comment) {
        $this->comment = $comment;
    }

    public function index(JsonResponse $res) {
        $controller = $this;
        $comments = $this->comment->orderBy('id', 'asc')->get();

        return new JsonResponse([
            'data' => $comments->map(function($comment) use ($controller) {
                return $controller->serializeComment($comment);
            }),
        ]);
    }

    public function find(JsonResponse $res, $id) {
        $comment = $this->comment->findOrFail($id);

        return new JsonResponse([
            'data' => $this->serializeComment($comment),
        ]);
    }

    public function store(Request $req, JsonResponse $res) {
        $type = $req->json('data.type');
        $attrs = $this->getAttrsFromRequest($req);

        if ($this->validateCreate($attrs)) {
            $comment = $this->comment->create($attrs);

            return new JsonResponse([
                'data' => $this->serializeComment($comment),
            ]);
        }

        return new JsonResponse([
            'errors' => array_map(function ($err) {
                return [
                    'status' => '400',
                    'title' => 'Invalid Attribute',
                    'detail' => $err,
                ];
            }, $this->errors->all()),
        ], 400);
    }

    public function update(Request $req, JsonResponse $res, $id) {
        $type = $req->json('data.type');
        $attrs = $this->getAttrsFromRequest($req);

        $comment = $this->comment->find($id);
        $comment->fill($attrs);
        $comment->save();

        return new JsonResponse([
            'data' => $this->serializeComment($comment),
        ]);
    }


    public function delete(JsonResponse $res, $id) {
        $this->comment->destroy($id);

        return new JsonResponse(null, 204);
    }

    protected function getAttrsFromRequest($req) {
        $attrs = $req->json('data.attributes');
        $attrs['blog'] = $req->json('data.relationships.blog.data.id');

        return $attrs;
    }

    protected function validateCreate($attrs) {
        $validator = Validator::make($attrs, $this->rules);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        return true;
    }

    protected function serializeComment($comment) {
        return [
            'type' => 'comments',
            'id' => (string) $comment->id,
            'attributes' => $comment->toArray(),
            'relationships' => $comment->getJSONRelationshipsArray(),
        ];
    }
}

<?php namespace Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'content',
        'blog',
    ];

    protected $hidden = [
        'id',
        'blog_id',
        'updated_at',
        'created_at',
    ];

    public function blog() {
        return $this->belongsTo(Blog::class);
    }

    public function setBlogAttribute($value) {
        \Log::info('Blog');
        $this->attributes['blog_id'] = $value;
    }

    public function getBlogAttribute() {
        return $this->attributes['blog_id'];
    }

    public function getJSONRelationshipsArray() {
        return [
            'blog' => [
                'data' => [
                    'type' => 'blogs',
                    'id' => (string) $this->blog,
                ],
            ],
        ];
    }
}

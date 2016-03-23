<?php namespace Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
    ];


    protected $hidden = [
        'id',
        'updated_at',
        'created_at',
    ];

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function getJSONRelationshipsArray() {
        return [
            'comments' => [
                'data' => $this->comments()->lists('id')->map(function ($score) {
                    return ['type' => 'comments', 'id' => (string) $score];
                }),
            ],
        ];
    }
}

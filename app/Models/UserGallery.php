<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGallery extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','user_id','gallery','gallery_original','gallery_thumbnail'
    ];
    /**
     * Activity log array
     *
     * @var array
     */
    public $activity_log = [  ];

    /**
     * Log Activity relationships array
     *
     * @var array
     */
    public $log_relations = [  ];

    
    /**
     * Lightweight response variable
     *
     * @var array
     */
    public $light = [ 'id', 'user_id', 'gallery' ];

    /**
     * Related permission array
     *
     * @var array
     */
    public $related_permission = [ 'users' ];

    /**
     * @var array
     */
    public $sortable = [ 'user_galleries.created_at', 'user_galleries.id',  ];

    /**
     * @var array
     */
    public $foreign_sortable = [ 'user_id' ];

    /**
     * @var array
     */
    public $foreign_table = [ 'users' ];

    /**
     * @var array
     */
    public $foreign_key = [ 'name' ];

    /**
     * @var array
     */
    public $foreign_method = [ 'user' ];
}

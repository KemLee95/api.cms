<div>
  Dear {{isset($name) && $name ? $name : "guys"}},
<div>

  @if (isset($posts) && $posts)
    <p>
      There are many posts that that’s have not read on that date:
    </p>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Post Id</th>
          <th scope="col">Author</th>
          <th scope="col">Email</th>
          <th scope="col">Tilte</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($posts as $post)
          <tr>
            <th scope="row">{{isset($post->post_id) && $post->post_id ? $post->post_id : "Not yet update"}}</th>
            <td>{{isset($post) && $post->user_name ? $post->user_name : "Not yet update"}}</td>
            <td>{{isset($post->user_email) && $post->user_email ? $post->user_email : "Not yet update"}}</td>
            <td>{{isset($post->title) && $post->title ? $post->title : "Not yet update"}}</td>
          </tr>
        @endforeach
      </tbody>
    </table> 
  @else
    There is no post that's have no reader on today.
  @endif
</div>

<div>
  Thanks and Best Regards
  ---------------------------------------------
  
  Le Tuan Khanh Em (Mr.)
  CAE Engineer
  Phone: 0358951057
  Email: ltkem2103@gmail.com
</div>
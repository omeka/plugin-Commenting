<?php
queue_css('commenting');
queue_js('commenting');
    head(array('title' => 'Comments', 'bodyclass' => 'primary'));

?>
<div id='primary'>

<h1>Comments</h1>
<table>
<thead>
<tr>
<th>Record</th>
<th>Author</th>
<th>Url</th>
<th>Email</th>
<th>Comment</th>
<th>Approved</th>
</tr>
</thead>
<tbody>
<?php foreach($this->comments as $comment): ?>
<tr>
<td><a href="<?php echo commenting_comment_uri($comment); ?>">Link</a></td>
<td><?php echo $comment->author_name; ?></td>
<td><?php echo $comment->author_url; ?></td>
<td><?php echo $comment->author_email; ?></td>
<td><?php echo $comment->body; ?></td>
<td><?php echo $comment->approved ? 'Yes' : "<span class='approve' id='approve-{$comment->id}'>Approve</span>"; ?></td>
</tr>
<?php endforeach; ?>
</tbody>

</table>
</div>

<?php foot(); ?>
<?php
queue_css('commenting');
queue_js('commenting');
    head(array('title' => 'Comments', 'bodyclass' => 'primary'));

?>
<div id='primary'>
<div class="pagination"><?php echo pagination_links(); ?></div>
    <?php echo flash(); ?>
    <div id="browse-meta" class="group">
        <div id="browse-meta-lists">
            <ul class="navigation">
                <li><strong>Quick Filter</strong></li>
            <?php
                echo nav(array(
                    'All' => uri('commenting/comment/browse'),
                    'Approved' => uri('commenting/comment/browse?approved=1'),
                    'Needs Approval' => uri('commenting/comment/browse?approved=0')
                ));
            ?>
            </ul>
        </div>
    </div>
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
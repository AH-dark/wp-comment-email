<?php
/**
 * Plugin Name: 评论邮件通知
 * Description: 评论时发送邮件通知上级评论作者
 * Version: 1.0.3
 * Author: AHdark
 * Author URI: https://ahdark.com
 */

//WordPress 评论回复邮件通知代码
add_action('comment_post', function ($comment_id) {
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent;
    $spam_confirmed = $comment->comment_approved;
    if (($parent_id != 0) && ($spam_confirmed != 'spam')) {
        $to = trim(get_comment($parent_id)->comment_author_email);
        if (!is_email($to)) {
            return;
        }

        $subject = trim(get_comment($parent_id)->comment_author) . '， 您在 ' . $blogname . ' 中的留言有新的回复啦！';
        /** @noinspection HtmlDeprecatedAttribute */
        $message = '<div style="color:#555;font:12px/1.5 微软雅黑,Tahoma,Helvetica,Arial,sans-serif;max-width:550px;margin:30px auto;border-top: none;" ><table border="0" cellspacing="0" cellpadding="0"><tbody><tr valign="top" height="2"><td valign="top"><div style="background-color:white;border-top:2px solid #00A7EB;box-shadow:0 1px 3px #AAAAAA;12px;max-width:550px;color:#555555;font-family:微软雅黑, Arial;;font-size:12px;">
<h2 style="border-bottom:1px solid #DDD;font-size:14px;font-weight:normal;padding:8px 0 10px 8px;">您在 <a style="text-decoration:none; color:#58B5F5;font-weight:600;" href="' . home_url() . '">' . $blogname . '</a> 的留言有回复啦！</h2><div style="padding:0 12px 0 12px;margin-top:18px">
<p>您好, ' . trim(get_comment($parent_id)->comment_author) . '！<br/>您发表在文章 《' . get_the_title($comment->comment_post_ID) . '》 的评论:</p>
<p style="background-color: #EEE;border: 1px solid #DDD;padding: 20px;margin: 15px 0;">' . get_comment($parent_id)->comment_content . '</p>
<p>@' . trim($comment->comment_author) . ' 给您的回复如下:</p>
<p style="background-color: #EEE;border: 1px solid #DDD;padding: 20px;margin: 15px 0;">' . nl2br(strip_tags($comment->comment_content)) . '</p>
<p>您可以点击 <a style="text-decoration:none; color:#5692BC" href="' . htmlspecialchars(get_comment_link($parent_id)) . '">查看完整的回复內容</a>，也欢迎再次光临 <a style="text-decoration:none; color:#5692BC"
href="' . home_url() . '">' . $blogname . '</a>。祝您生活愉快！</p>
<p style="padding-bottom: 15px;">(此邮件由系统自动发出,请勿直接回复!)</p></div></div></td></tr></tbody></table></div>';
        $from = "From: \"" . get_option('blogname') . "\" <no-reply@ahdark.com>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail($to, $subject, $message, $headers);
    } else if ($parent_id == 0 && $spam_confirmed != 'spam') {
        $post = get_post($comment->comment_post_ID);
        if ($post->ID == $comment_id) {
            return;
        }
        $to = trim(get_userdata($post->post_author)->user_email);
        if (!is_email($to)) {
            return;
        }

        $subject = trim(get_userdata($post->post_author)->display_name) . '， 您在 ' . $blogname . ' 中的文章《' . trim($post->post_title) . '》有新的回复啦！';
        /** @noinspection HtmlDeprecatedAttribute */
        $message = '<div style="color:#555;font:12px/1.5 微软雅黑,Tahoma,Helvetica,Arial,sans-serif;max-width:550px;margin:30px auto;border-top: none;" ><table border="0" cellspacing="0" cellpadding="0"><tbody><tr valign="top" height="2"><td valign="top"><div style="background-color:white;border-top:2px solid #00A7EB;box-shadow:0 1px 3px #AAAAAA;12px;max-width:550px;color:#555555;font-family:微软雅黑, Arial;;font-size:12px;">
<h2 style="border-bottom:1px solid #DDD;font-size:14px;font-weight:normal;padding:8px 0 10px 8px;">您在 <a style="text-decoration:none; color:#58B5F5;font-weight:600;" href="' . home_url() . '">' . $blogname . '</a> 的留言有回复啦！</h2><div style="padding:0 12px 0 12px;margin-top:18px">
<p>您好, ' . trim(get_userdata($post->post_author)->display_name) . '！<br/>您发表的文章 《' . get_the_title($comment->comment_post_ID) . '》 中，</p>
<p>@' . trim($comment->comment_author) . ' 给您的回复如下:</p>
<p style="background-color: #EEE;border: 1px solid #DDD;padding: 20px;margin: 15px 0;">' . nl2br(strip_tags($comment->comment_content)) . '</p>
<p>您可以点击 <a style="text-decoration:none; color:#5692BC" href="' . htmlspecialchars(get_comment_link($comment_id)) . '">查看完整的回复內容</a>，也欢迎再次光临 <a style="text-decoration:none; color:#5692BC"
href="' . home_url() . '">' . $blogname . '</a>。祝您生活愉快！</p>
<p style="padding-bottom: 15px;">(此邮件由系统自动发出,请勿直接回复!)</p></div></div></td></tr></tbody></table></div>';
        $from = "From: \"" . get_option('blogname') . "\" <no-reply@ahdark.com>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail($to, $subject, $message, $headers);
    }
});

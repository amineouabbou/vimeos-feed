<?php
// Load the autoloader
if (file_exists('../../api/autoload.php')) {
    require_once('../../api/autoload.php');
}else{
}



// Load the configuration file.
if (!function_exists('json_decode')) {
    throw new Exception('We could not find json_decode. json_decode is found in php 5.2 and up, but not found on many linux systems due to licensing conflicts. If you are running ubuntu try "sudo apt-get install php5-json".');
}else{
    $config = json_decode(file_get_contents('../../config.json'), true);
}



// Check if the configuration file is not empty
if (empty($config['client_id']) || empty($config['client_secret']) || empty($config['access_token'])) {
    throw new Exception('We could not locate your client id or client secret in "' . __DIR__ . '/config.json". Please create one, and reference config.json.example');
}else{
    $lib = new \Vimeo\Vimeo($config['client_id'], $config['client_secret'], $config['access_token']);
}


// Check if the page parameter is set
// If not set the default value of page to 1
$page = 1;
if (isset($_GET['page'])){
    $page = $_GET['page'];
}



// Check if the per_page parameter is set
// If not set the default value of per_page to 10
$per_page = 10;
if (isset($_GET['per_page'])){
    $per_page = $_GET['per_page'];
}



// Query parameter
// We use it to search in  the title & descipriton fields
$query = null;
if (isset($_GET['query']) && !empty($_GET['query'])){
    $query = $_GET['query'];
}


// Get the results
$videos = $lib->request('/channels/top/videos', array('page' => $page, 'per_page' => $per_page, 'query' => $query, 'sort' => 'comments', 'direction' => 'desc'));
?>


<?php
// Get the root URL
$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/vimeos-feed'; ?>


<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Vimeos Feeds</title>
        <link rel="stylesheet" href="../../css/styles.css">
    </head>
    <body>
        <div class="vimeos_wrap">
            <div class="logo">
                <a href="videos.php"><img src="../../images/logo.png"></a>
            </div>

            <div class="vimeos_filter">
                <form method="get">
                    <input type="hidden" name="page" value="<?php echo $videos['body']["page"]; ?>">
                    <div class="forms-list row">
                        <div class="field col-sm-12">
                            <label>View</label>
                            <div class="input-box select">
                                <select name="per_page">
                                    <?php
                                    $options = array( '10', '25', '50' );
                                    foreach($options as $option)
                                    {
                                        if($option == $_GET['per_page'])
                                        {
                                            echo "<option selected='selected' value='".$option."'>".$option."</option>";
                                        }
                                        else
                                        {
                                            echo "<option value='".$option."'>".$option."</option>";
                                        }

                                    }
                                    ?>
                                </select>            
                            </div><!--end input-box-->
                        </div><!--end field-->

                        <div class="field col-sm-12">
                            <label>Filter by desription</label>
                            <div class="input-box">
                                <textarea name="query" value=""><?php if (isset($_GET['query']) && !empty($_GET['query'])){ echo $_GET['query']; } ?></textarea>
                            </div><!--end input-box-->
                        </div><!--end field-->

                        <div class="send_query col-sm-12">
                            <button class="button" type="submit"><span>Filter</span></button>
                        </div>
                    </div>
                </form>
            </div><!--end vimeos_filter-->

            <div class="vimeos_feed">
                <div class="vimeos_top_section">
                    <h3>Most liked</h3>
                    <div class="filter_by">
                        <a href="#" class="toggle-filter"><span><i class="fa fa-filter"></i> Filter by</span></a>
                    </div><!--end filter_by--->
                </div><!--end vimeos_top_section-->

                <div class="video_items">
                    <?php foreach ( $videos['body']['data'] as $video ) { ?>
                    <div class="video_item">
                        <div class="video_thumbnail">

                            <?php
                            //Get the video ID by removing a part
                            // of the video Uri
                            $video_id = str_replace("/videos/", "", $video['uri']); ?>

                            <?php if($video['user']['pictures']['sizes']['2']['link']) { ?>
                            <a target="_blank" href="http://www.vimeo.com/<?php echo $video_id; ?>"><img src="<?php echo $video['user']['pictures']['sizes']['2']['link']; ?>" alt="<?php echo $video['name']; ?>"></a>
                            <?php } else { ?>
                            &nbsp;
                            <?php } ?>
                        </div>

                        <div class="content_box">
                            <div class="video_title"><a target="_blank" title="<?php echo $video['name']; ?>" href="http://www.vimeo.com/<?php echo $video_id; ?>"><strong><?php echo $video['name']; ?></strong></a></div>
                            <p class="description">
                                <?php
                                    $max_length = 140;
                                    if (strlen($video['description']) > $max_length)
                                    {
                                        $offset = ($max_length - 3) - strlen($video['description']);
                                        $video_desc = substr($video['description'], 0, strrpos($video['description'], ' ', $offset)) . '...';
                                        echo $video_desc;
                                    }
                                ?>
                            </p>

                            <div class="bottom_infos">
                                <div class="views"><span><i class="fa fa-eye"></i> <?php echo $video['stats']['plays']; ?></span></div>
                                <div class="likes"><span><i class="fa fa-comment"></i> <?php echo $video['metadata']['connections']['comments']['total']; ?></span></div>
                                <div class="comments"><span><i class="fa fa-heart"></i> <?php echo $video['metadata']['connections']['likes']['total']; ?></span></div>
                            </div><!--end bottom_infos-->
                        </div><!--end content_box-->
                    </div><!--end video_item-->
                    <?php } ?>
                </div><!--end video_items-->
            </div><!--end vimeos_feed-->

            <?php if($videos['body']['paging']['next'] !== NULL) { ?>
            <div class="vimeos_pagination">
                <a class="primary_link" href="<?php echo $root; ?><?php echo $videos['body']['paging']['next']; ?>"><span>Next</span></a>
            </div>
            <?php } ?>
        </div><!--end vimeos_wrap -->

    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="../../js/main.js"></script>
</html>
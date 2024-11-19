<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Increase maximum execution time and memory limit if needed
ini_set('max_execution_time', '300');
ini_set('memory_limit', '512M');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Handle deletion if tempId is passed
    if (isset($_POST['tempId'])) {
        $tempId = $_POST['tempId'];
        $tempFilePath = __DIR__ . '/temp.json';

        if (file_exists($tempFilePath)) {
            $tempData = json_decode(file_get_contents($tempFilePath), true);

            if (isset($tempData[$tempId])) {
                // Delete related data using temp data
                $slug = $tempData[$tempId]['slug'];

                // Delete the HTML file
                $postFileName = __DIR__ . '/' . $slug . '.html';
                if (file_exists($postFileName)) {
                    unlink($postFileName);
                }

                // Delete the featured image
                $featuredImagePath = str_replace('https://dranjalisayurveda.com/blog/', __DIR__ . '/', $tempData[$tempId]['featuredImage']);
                if (file_exists($featuredImagePath)) {
                    unlink($featuredImagePath);
                }

                // Delete from timestamp.json
                $timestampFilePath = __DIR__ . '/timestamp.json';
                if (file_exists($timestampFilePath)) {
                    $timestampData = json_decode(file_get_contents($timestampFilePath), true);
                    foreach ($timestampData as $timestamp => $data) {
                        if ($data['slug'] === $slug) {
                            unset($timestampData[$timestamp]);
                            file_put_contents($timestampFilePath, json_encode($timestampData, JSON_PRETTY_PRINT));
                            break;
                        }
                    }
                }

                // Delete from tags.json
                $tagsFilePath = __DIR__ . '/tags.json';
                if (file_exists($tagsFilePath)) {
                    $tagsData = json_decode(file_get_contents($tagsFilePath), true);
                    foreach ($tagsData['hashtags'] as $tag => $posts) {
                        if (isset($posts[$slug . '.html'])) {
                            unset($tagsData['hashtags'][$tag][$slug . '.html']);
                            if (empty($tagsData['hashtags'][$tag])) {
                                unset($tagsData['hashtags'][$tag]);
                            }
                        }
                    }
                    file_put_contents($tagsFilePath, json_encode($tagsData, JSON_PRETTY_PRINT));
                }

                // Remove temp data after successful deletion
                unset($tempData[$tempId]);
                file_put_contents($tempFilePath, json_encode($tempData, JSON_PRETTY_PRINT));
            }
        }
    }

    // Ensure all form fields are present
    $required_fields = ['title', 'content', 'focusKeyphrase', 'seoTitle', 'slug', 'metaDescription', 'tags', 'visibility', 'category'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            die("Error: Missing $field");
        }
    }

    // Get form data
    $title = htmlspecialchars($_POST['title']);
    $content = $_POST['content'];
    $focusKeyphrase = htmlspecialchars($_POST['focusKeyphrase']);
    $seoTitle = htmlspecialchars($_POST['seoTitle']);
    $slug = htmlspecialchars($_POST['slug']);
    $metaDescription = htmlspecialchars($_POST['metaDescription']);
    $canonicalUrl = isset($_POST['canonicalUrl']) && !empty($_POST['canonicalUrl']) ? htmlspecialchars($_POST['canonicalUrl']) : $rootPath . $slug ;
    $headScriptsInput = $_POST['headSrcipts'];
    $bodyScripts = $_POST['bodySrcipts'];
    $structuredDataInput = $_POST['structuredData'];
    $otherHeadScripts = $_POST['otherHeadScripts'];
    $tags = $_POST['tags'];
    $visibility = $_POST['visibility'];
    $category = htmlspecialchars($_POST['category']); // New category field
    // New geo-location fields
    $geoRegion = htmlspecialchars($_POST['geoRegion']);
    $geoPlacename = htmlspecialchars($_POST['geoPlacename']);
    $geoPosition = htmlspecialchars($_POST['geoPosition']);
    $ICBM = htmlspecialchars($_POST['ICBM']);
    // Check if a custom timestamp was provided
    if (!empty($_POST['timestamp'])) {
        // Use the custom timestamp provided by the user
        $publishDateTime = date('c', strtotime($_POST['timestamp']));
        $formattedPublishDate = date('F j, Y', strtotime($_POST['timestamp'])); // Format for display
    } else {
        // Use the current date and time as the default
        $publishDateTime = date('c');
        $formattedPublishDate = date('F j, Y'); // Default formatting
    }

    // Extract the first line from the content
    $plainTextContent = strip_tags($content);
    $firstLine = substr($plainTextContent, 0, 100);
    $wordCount = str_word_count($plainTextContent); // Calculate word count

    // Handle image upload
    $targetDir = "uploads/";
    $featuredImage = "";

    // Check if the post is being edited
    $isEditing = isset($_POST['isEditing']) && $_POST['isEditing'] === 'true';

    if (!empty($_FILES['featuredImage']['name'])) {
        // If a new image is uploaded, process the image
        $targetFile = $targetDir . basename($_FILES["featuredImage"]["name"]);
        if (move_uploaded_file($_FILES["featuredImage"]["tmp_name"], $targetFile)) {
            $featuredImage = $targetFile;
        } else {
            echo"<script type='text/javascript'>alert('Invalid request method.');</script>";
            // die("Error: Unable to upload image.");
        }
    } else {
        // If no new image is uploaded and this is an edit, retain the existing image
        if ($isEditing) {
            $timestampFilePath = __DIR__ . '/timestamp.json';
            if (file_exists($timestampFilePath)) {
                $timestampData = json_decode(file_get_contents($timestampFilePath), true);
                foreach ($timestampData as $timestamp => $data) {
                    if ($data['slug'] === $slug) {
                        $featuredImage = str_replace($data['featuredImage']);
                        break;
                    }
                }
            }
        }
    }

    // If the featured image is still empty, ensure it's not accidentally cleared
    if (empty($featuredImage)) {
        $timestampFilePath = __DIR__ . '/timestamp.json';
            if (file_exists($timestampFilePath)) {
                $timestampData = json_decode(file_get_contents($timestampFilePath), true);
                foreach ($timestampData as $timestamp => $data) {
                    if ($data['slug'] === $slug) {
                        $featuredImage = str_replace($rootPath, '', $data['featuredImage']);
                        break;
                    }
                }
            }
    }

    // Get form data
    $category = htmlspecialchars($_POST['category']);

    // Load existing categories
    $categoriesFilePath = __DIR__ . '/categories.json';
    if (file_exists($categoriesFilePath)) {
        $categoriesData = json_decode(file_get_contents($categoriesFilePath), true);

        // If the category doesn't exist, add it to categories.json
        if (!in_array($category, $categoriesData['categories'])) {
            $categoriesData['categories'][] = $category;
            file_put_contents($categoriesFilePath, json_encode($categoriesData, JSON_PRETTY_PRINT));
        }
    }

    // User-defined global variables
    $domainName = 'https://dranjalisayurveda.com/';
    $rootPath = 'https://dranjalisayurveda.com/blog/';
    $language = 'en_US';
    $openGraphType = 'article';
    $publisherUrl = 'https://www.facebook.com/';
    $publisherName = 'Dr.Anjalis Ayurvedic Centre';
    $publisherTwitterId = '@dr_anjalis_ayurvedic_center';
    $publisherLogo = 'https://dranjalisayurveda.com/blog/globals/logo.webp';
    $publisherTagline = 'Experience Ayurvedas healing at Anjalis Center. Personalized therapies and holistic guidance await.';
    $favioconLink = 'https://dranjalisayurveda.com/img/favicon.jpeg';
    $blogHome = 'https://dranjalisayurveda.com/blog.html';
    $facebookProfileLink = 'https://www.facebook.com/DrAnjalisAyurvedicCenter/';
    $instagramProfileLink = 'https://www.instagram.com/dr_anjalis_ayurvedic_center/';
    $threadsProfileLink = 'https://www.threads.net/@dr_anjalis_ayurvedic_center';
    $twitterProfileLink = 'https://x.com/AnjaliAyurvedic';
    $linkedinProfileLink = 'https://www.linkedin.com/company/dr-anjali-s-ayurvedic-center';
    $whatsappProfileLink = 'https://wa.me/+971524731447';
    $youtubeProfileLink = 'https://www.youtube.com/@Dr.AnjalisAyurvedicCenter-q2q';
    $publisherAddress = 'Shop No: S12 & S13,
                            Bldg. No. R005-1, Wasl Hub,
                            Al Karama - Dubai.';
    $publisherMobile = '+97142992881';
    $publisherEmail = 'info@dranjalisayurveda.com';
    $privacyPolicy = 'https://dranjalisayurveda.com/privacy-policy.html';
    $termsAndCondition = 'https://dranjalisayurveda.com/terms-and-condition.html';
    $siteMap = 'https://dranjalisayurveda.com/sitemap.html';

    // Processed variables
    // $canonicalUrl = $rootPath . $slug . '.html';
    $CurrentDateTime = date('c');
    $featuredImageUrl = $rootPath . $featuredImage;
    $logoImageUrl = $rootPath . $publisherLogo;
    // $formattedPublishDate = date('F j, Y');
    $blogHomeUrl = $domainName . $blogHome;
    $privacyPolicyUrl = $domainName . $privacyPolicy;
    $termsAndConditionUrl = $domainName . $termsAndCondition;
    $siteMapUrl = $domainName . $siteMap;
    $categoryLinks = '<a href="categories.html?category=' . urlencode($category) . '">' . htmlspecialchars($category) . '</a>';
    $headScriptsInput = isset($_POST['headSrcipts']) ? $_POST['headSrcipts'] : ''; // Check if the field is set
    $structuredDataInput = isset($_POST['structuredData']) ? $_POST['structuredData'] : ''; // Check if the field is set

    

    // Read the existing tags.json file
    $tagsFilePath = __DIR__ . "/tags.json";
    $tagsData = file_exists($tagsFilePath) ? json_decode(file_get_contents($tagsFilePath), true) : ["hashtags" => []];

    // Process each tag and update the tags.json structure
    $tagsArray = explode(',', $tags);
    
    $formattedTagsForJson = array_map(function($tag) {
        $tag = trim($tag);
        if (strpos($tag, '#') !== 0) {
            $tag = '#' . $tag;
        }
        return $tag;
    }, $tagsArray);
    $formattedTagsString = implode(',', $formattedTagsForJson);





    if (!empty($headScriptsInput)) {
        // If the structuredDataInput is not empty, use the user's input
        $headScripts = $headScriptsInput;
    } else {
$headScriptsTemplate = '
        <title>$title</title>
        <meta name="description" content="$metaDescription" />
        <meta name="robots" content="$robotsMeta" />
        <meta name="geo.region" content="$geoRegion" />
        <meta name="geo.placename" content="$geoPlacename" />
        <meta name="geo.position" content="$geoPosition" />
        <meta name="ICBM" content="$ICBM" />
        <link rel="shortcut icon" type="image/jpg" href="$favioconLink" />
        <link rel="canonical" href="$canonicalUrl" />
        <meta property="og:locale" content="$language" />
        <meta property="og:type" content="$openGraphType" />
        <meta property="og:title" content="$seoTitle" />
        <meta property="og:description" content="$metaDescription" />
        <meta property="og:url" content="$canonicalUrl" />
        <meta property="article:publisher" content="$publisherUrl" />
        <meta property="article:published_time" content="$CurrentDateTime" />
        <meta name="author" content="$publisherName" />
        <meta property="og:image:type" content="image/jpeg" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:creator" content="$publisherTwitterId" />
        <meta name="twitter:site" content="$publisherTwitterId" />
        <meta name="twitter:label1" content="Written by" />
        <meta name="twitter:data1" content="$publisherName" />
        <meta name="twitter:label2" content="Est. reading time" />
        <meta name="twitter:data2" content="4 minutes" />
        ';

                
        // Replace the placeholders with actual PHP variables
        $headScripts = str_replace(
            ['$title', '$robotsMeta', '$geoRegion', '$geoPlacename', '$geoPosition', '$ICBM', '$favioconLink', '$metaDescription', '$canonicalUrl', '$language', '$openGraphType', '$seoTitle', '$metaDescription', '$canonicalUrl', '$publisherUrl', '$CurrentDateTime', '$publisherName', '$publisherTwitterId'],
            [$title, $robotsMeta, $geoRegion, $geoPlacename, $geoPosition, $ICBM, $favioconLink, $metaDescription, $canonicalUrl, $language, $openGraphType, $seoTitle, $metaDescription, $canonicalUrl, $publisherUrl, $CurrentDateTime, $publisherName, $publisherTwitterId],
            $headScriptsTemplate
        );

    }




    if (!empty($structuredDataInput)) {
        // If the structuredDataInput is not empty, use the user's input
        $structuredData = $structuredDataInput;
    } else {
        $structuredDataTemplate = '
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@graph": [
                    {
                        "@type": "$openGraphType",
                        "@id": "$canonicalUrl/#$openGraphType",
                        "isPartOf": {
                            "@id": "$canonicalUrl/"
                        },
                        "author": {
                            "name": "$publisherName",
                            "@id": "$blogHomeUrl"
                        },
                        "headline": "$title",
                        "datePublished": "$publishDateTime",
                        "mainEntityOfPage": {
                            "@id": "$canonicalUrl/"
                        },
                        "wordCount": "$wordCount",
                        "commentCount": 0,
                        "publisher": {
                            "@id": "$blogHomeUrl"
                        },
                        "image": {
                            "@id": "$canonicalUrl/#primaryimage"
                        },
                        "thumbnailUrl": "$featuredImageUrl",
                        "keywords": [
                            $formattedTagsString
                        ],
                        "articleSection": [
                            "Blog"
                        ],
                        "inLanguage": "$language"
                    },
                    {
                        "@type": "WebPage",
                        "@id": "$canonicalUrl/",
                        "url": "$canonicalUrl/",
                        "name": "$seoTitle",
                        "isPartOf": {
                            "@id": "$blogHomeUrl"
                        },
                        "primaryImageOfPage": {
                            "@id": "$canonicalUrl/#primaryimage"
                        },
                        "image": {
                            "@id": "$canonicalUrl/#primaryimage"
                        },
                        "thumbnailUrl": "$featuredImageUrl",
                        "datePublished": "$publishDateTime",
                        "description": "$metaDescription.",
                        "breadcrumb": {
                            "@id": "$canonicalUrl/#breadcrumb"
                        },
                        "inLanguage": "$language",
                        "potentialAction": [
                            {
                                "@type": "ReadAction",
                                "target": [
                                    "$canonicalUrl/"
                                ]
                            }
                        ]
                    },
                    {
                        "@type": "ImageObject",
                        "inLanguage": "$language",
                        "@id": "$canonicalUrl/#primaryimage",
                        "url": "$featuredImageUrl",
                        "contentUrl": "$featuredImageUrl",
                        "caption": "$title"
                    },
                    {
                        "@type": "BreadcrumbList",
                        "@id": "$canonicalUrl/#breadcrumb",
                        "itemListElement": [
                            {
                                "@type": "ListItem",
                                "position": 1,
                                "name": "Home",
                                "item": "$blogHomeUrl"
                            },
                            {
                                "@type": "ListItem",
                                "position": 2,
                                "name": "$title"
                            }
                        ]
                    },
                    {
                        "@type": "WebSite",
                        "@id": "$blogHomeUrl/#website",
                        "url": "$blogHomeUrl/",
                        "name": "$publisherName",
                        "description": "$publisherTagline",
                        "publisher": {
                            "@id": "$blogHomeUrl/#organization"
                        },
                        "inLanguage": "$language"
                    },
                    {
                        "@type": "Organization",
                        "@id": "$blogHomeUrl/#organization",
                        "name": "$publisherName",
                        "alternateName": "$publisherName",
                        "url": "$blogHomeUrl",
                        "logo": {
                            "@type": "ImageObject",
                            "inLanguage": "$language",
                            "@id": "$blogHomeUrl",
                            "url": "$logoImageUrl",
                            "contentUrl": "$logoImageUrl",
                            "caption": "$publisherName"
                        },
                        "image": {
                            "@id": "$blogHomeUrl"
                        },
                        "sameAs": [
                            "$facebookProfileLink",
                            "$threadsProfileLink",
                            "$instagramProfileLink",
                            "$linkedinProfileLink"
                        ]
                    },
                    {
                        "@type": "Person",
                        "@id": "$blogHomeUrl",
                        "name": "$publisherName"
                    }
                ]
            }
            </script>
        ';
 
        
        
        // Replace the placeholders with actual PHP variables
        $structuredData = str_replace(
            ['$wordCount', '$openGraphType', '$canonicalUrl', '$publisherName', '$blogHomeUrl', '$title', '$publishDateTime', '$featuredImageUrl', '$formattedTagsString', '$language', '$seoTitle', '$metaDescription', '$publisherTagline', '$logoImageUrl', '$facebookProfileLink', '$threadsProfileLink', '$instagramProfileLink', '$linkedinProfileLink'],
            [$wordCount, $openGraphType, $canonicalUrl, $publisherName, $blogHomeUrl, $title, $publishDateTime, $featuredImageUrl, $formattedTagsString, $language, $seoTitle, $metaDescription, $publisherTagline, $logoImageUrl, $facebookProfileLink, $threadsProfileLink, $instagramProfileLink, $linkedinProfileLink],
            $structuredDataTemplate
        );

    }

    $postFileName = $slug . ".html"; // The name of the HTML file being created
    foreach ($tagsArray as $tag) {
        $tag = trim($tag); // Trim any whitespace around the tag
        if (!isset($tagsData["hashtags"][$tag])) {
            $tagsData["hashtags"][$tag] = [];
        }

        // Append or update the data under the filename
        $tagsData["hashtags"][$tag][$postFileName] = [
            "title" => $title,
            "featuredImage" => $featuredImageUrl,
            "url" => $canonicalUrl,
            "category" => $category, // Include category in tags.json
            "visibility" => $visibility
        ];
    }

    // Remove tags no longer associated with the post
    foreach ($tagsData['hashtags'] as $tag => $posts) {
        if (!in_array($tag, $tagsArray)) {
            unset($tagsData['hashtags'][$tag][$postFileName]);
            if (empty($tagsData['hashtags'][$tag])) {
                unset($tagsData['hashtags'][$tag]);
            }
        }
    }

    // Write the updated data back to tags.json
    if (file_put_contents($tagsFilePath, json_encode($tagsData, JSON_PRETTY_PRINT)) === false) {
        die("Error: Unable to update tags.json.");
    }

    // Handle timestamp.json for recent posts
    $timestampFilePath = __DIR__ . "/timestamp.json";
    $timestampData = file_exists($timestampFilePath) ? json_decode(file_get_contents($timestampFilePath), true) : [];

    // Check if this post already exists in timestamp.json (by slug or URL)
    $existingTimestamp = null;
    foreach ($timestampData as $timestamp => $data) {
        if ($data['slug'] === $slug) {
            $existingTimestamp = $timestamp;
            break;
        }
    }

    // If the post exists, remove the old entry and delete associated files
    if ($existingTimestamp) {
        unset($timestampData[$existingTimestamp]);
        $existingPostFile = __DIR__ . "/" . $slug . ".html";
        if (file_exists($existingPostFile)) {
            unlink($existingPostFile); // Delete the old HTML file
        }

        // If a new image is uploaded, delete the old one
        if (!empty($_FILES['featuredImage']['name'])) {
            $oldImage = str_replace($rootPath, '', $timestampData[$existingTimestamp]['featuredImage']);
            $oldImagePath = __DIR__ . "/" . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old featured image
            }
        }
    }

// Capture the robotsMeta value from the form submission
$robotsMeta = isset($_POST['robotsMetaInput']) ? $_POST['robotsMetaInput'] : 'index, follow';

    $geoRegion = htmlspecialchars($_POST['geoRegion']);
    $geoPlacename = htmlspecialchars($_POST['geoPlacename']);
    $geoPosition = htmlspecialchars($_POST['geoPosition']);
    $ICBM = htmlspecialchars($_POST['ICBM']);
    
    $timestampData[$publishDateTime] = [
        "title" => $title,
        "featuredImage" => $featuredImage,
        "url" => $canonicalUrl,
        "firstLine" => $firstLine,
        "content" => $content,
        "focusKeyphrase" => $focusKeyphrase,
        "seoTitle" => $seoTitle,
        "slug" => $slug,
        "metaDescription" => $metaDescription,
        "tags" => $tags,
        "visibility" => $visibility,
        "category" => $category,
        "robotsMeta" => $robotsMeta, // Ensure this is saved
        "geoRegion" => $geoRegion,
        "geoPlacename" => $geoPlacename,
        "geoPosition" => $geoPosition,
        "ICBM" => $ICBM,
        "canonicalUrl" => $canonicalUrl, // Save canonical URL in timestamp.json
        "headScripts" => $headScripts,   // New key for head scripts
        "otherHeadScripts" => $otherHeadScripts,
        "bodyScripts" => $bodyScripts,    // New key for body scripts
        "structuredData" => $structuredData,
        "timestamp" => $publishDateTime
    ];

    // Write the updated data back to timestamp.json
    if (file_put_contents($timestampFilePath, json_encode($timestampData, JSON_PRETTY_PRINT)) === false) {
        die("Error: Unable to update timestamp.json.");
    }

    // Generate hashtag links
    $tagLinks = array_map(function($tag) {
        return '<a href="hashtagposts.html?tag=' . urlencode(trim($tag)) . '"> ' . htmlspecialchars(trim($tag)) . '</a>';
    }, $tagsArray);
    $tagLinksString = implode(', ', $tagLinks);

    // Create category links
    // $categoryLinks = '<a href="categories.html?category=blog">Blog</a>, <a href="categories.html?category=case%20study">Case Study</a>';

    if ($visibility === 'public') {
        // Generate hashtag links
        $tagLinks = array_map(function($tag) {
            return '<a href="hashtagposts.html?tag=' . urlencode(trim($tag)) . '"> ' . htmlspecialchars(trim($tag)) . '</a>';
        }, $tagsArray);
        $tagLinksString = implode(', ', $tagLinks);
    

        // Check if robotsMeta is present in the form submission
        if (isset($_POST['robotsMeta'])) {
            $robotsMeta = htmlspecialchars($_POST['robotsMeta']);
        } else {
            // Default to 'index, follow' if not provided
            $robotsMeta = 'index, follow';
        }
        
        // Create the blog post content with updated styling and hashtag links
        $blogPostContent = <<<HTML
        <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        $headScripts
        $structuredData
        $otherHeadScripts
        
        <!-- <link rel="stylesheet" href="blog.css"/> -->
        <link rel="stylesheet" href="stylesheet.css"/>
        <link rel="stylesheet" href="ql.css">

        <!--==============================
	    All CSS File
	============================== -->
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Fontawesome Icon -->
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="../assets/css/magnific-popup.min.css">
    <!-- Slick Slider -->
    <link rel="stylesheet" href="../assets/css/slick.min.css">
    <!-- odometer -->
    <link rel="stylesheet" href="../assets/css/odometer-theme-default.css">
    <!-- flipster -->
    <link rel="stylesheet" href="../assets/css/jquery.flipster.min.css">
    <!-- datetimepicker -->
    <link rel="stylesheet" href="../assets/css/jquery.datetimepicker.min.css">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="shortcut icon" type="image/jpg" href="../assets/img/favicons/favicon.jpg">

    <!--==============================
	  Google Fonts
	============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jomhuria&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://kit.fontawesome.com/6c53136549.js" crossorigin="anonymous"></script>

        <script src="recentposts.js"></script> <!-- Add this line to include the recentposts.js script -->
    </head>
    <body>
    <!--==============================
    Mobile Menu
  ============================== -->
    <div class="th-menu-wrapper">
        <div class="th-menu-area">
            <div class="mobile-logo">
                <a href="../index.html"><img style="width: 80%;"
                        src="../assets/img/Dr. Anjali's Ayurvedic Center Logo.webp"
                        alt="Dr. Anjali's Ayurvedic Center Logo Design"></a>
                <div class="close-menu">
                    <button class="th-menu-toggle"><i class="fal fa-times"></i></button>
                </div>
            </div>
            <div class="th-mobile-menu">
                <ul>
                    <li>
                        <a href="../index.html">Home</a>
                    </li>
                    <li>
                        <a href="../about.html">About us</a>
                    </li>
                    <li>
                        <a href="../service.html">Services</a>
                    </li>
                    <li>
                        <a href="../specialties.html">Our Specialties</a>
                    </li>
                    <li>
                        <a href="../doctors.html">Our Doctors</a>
                    </li>
                    <li>
                        <a href="../packages.html">Packages</a>
                    </li>
                    <li>
                        <a href="index.html">Blog</a>
                    </li>
                    <li>
                        <a href="../contact.html">Contact Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </div><!--==============================
Header Area
==============================-->
    <header class="th-header header-layout1">
        <div class="header-top">
            <div class="container th-container">
                <div class="row justify-content-end justify-content-lg-between align-items-center gy-2">
                    <div class="col-auto d-none d-lg-block">
                        <div class="header-links">
                            <ul>
                                <li><a href=""><i style="color: #fff;" class="fa-solid fa-location-dot"></i> Dubai,
                                        United Arab Emirates</a></li>
                                <li><a href=""><i style="color: #fff;" class="fa-solid fa-envelope"></i>
                                        info@dranjalisayurveda.com</a></li>
                                <li><a href="tel:+97142992881"><i style="color: #fff;"
                                            class="fa-solid fa-phone"></i>+97142992881</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="social-links"><span style="color: #fff;" class="social-title">Follow Us:</span>
                            <a target="_blank" href="https://www.facebook.com/DrAnjalisAyurvedicCenter/"><i
                                    style="color: #fff;" class="fab fa-facebook-f"></i></a>
                            <a target="_blank" href="https://www.instagram.com/dr_anjalis_ayurvedic_center/"><i
                                    style="color: #fff;" class="fab fa-instagram"></i></a>
                            <a target="_blank" href="https://x.com/AnjaliAyurvedic"><i style="color: #fff;"
                                    class="fa-brands fa-x-twitter"></i> </a>
                            <a target="_blank" href="https://www.linkedin.com/company/dr-anjali-s-ayurvedic-center"><i
                                    style="color: #fff;" class="fa-brands fa-linkedin"></i> </a>
                            <a target="_blank" href="https://www.threads.net/@dr_anjalis_ayurvedic_center"><i
                                    style="color: #fff;" class="fa-brands fa-threads"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-wrapper">
            <!-- Main Menu Area -->
            <div class="menu-area">
                <div style="margin: 0 !important; width: 100% !important;" class="container th-container">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-md-2 col-sm-4 col-4">
                            <div class="header-logo">
                                <a href="../index.html">
                                    <img style="width: 80%;" src="../assets/img/Dr. Anjali's Ayurvedic Center Logo.webp"
                                        alt="Dr. Anjali's Ayurvedic Center Logo Design">
                                </a>
                            </div>
                        </div>
                        <div class="col-auto me-xl-auto">
                            <nav class="main-menu d-none d-lg-inline-block">
                                <ul>
                                    <li class="menu-item-has-children">
                                        <a href="../index.html">Home</a>
                                    </li>
                                    <li><a href="../about.html">About Us</a></li>
                                    <li><a href="../service.html">Services</a>
                                    </li>
                                    <li><a href="../specialties.html">Our Specialties</a>
                                    </li>
                                    <li><a href="../doctors.html">Our Doctors</a></li>
                                    <li><a href="../packages.html">Packages</a></li>
                                    <li><a href="index.html">Blog</a></li>
                                    <li><a href="../contact.html">Contact Us</a></li>
                                </ul>

                            </nav>
                            <button class="th-menu-toggle d-inline-block d-lg-none"><i
                                    class="fa-solid fa-bars"></i></button>
                        </div>
                        <!-- <div class="col-md-2 d-none d-xl-block">
                    <div class="header-button">
                        <a href="../index.html#contact-sec" class="th-btn">Book Appointment</a>
                    </div>
                </div> -->
                    </div>
                </div>
                <div class="logo-bg"></div>
            </div>
        </div>
    </header>



    <div class="row base_container">
        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 col-12 base_container_col1">
            <div class="container">
                <img src="$featuredImage" class="featured-image" alt="Featured Image">
                <h1 class="post-title">$title</h1>
                <p class="post-meta">By $publisherName | Published on $formattedPublishDate</p>
                <div class="post-content">$content</div>
                <p class="post-tags">Tags: $tagLinksString</p>
                <p class="post-categories">Category: $categoryLinks</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 base_container_col2">
            <h3>Recent posts:</h3>
                <div class="recentpost_card">
                    <h5><!--title of the latest post title of the latest post--> </h5>
                    <img src="url to featured image" alt="">
                    <p><!-- first line of the blogpost appears here--></p>
                    <a href="">Read more</a>
                </div>
                <!-- recent posts cards appear here like this -->
        </div>
    </div>

    <!--==============================
			Footer Area
==============================-->
    <footer class="footer-wrapper footer-layout1">
        <div class="widget-area top_borderline">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-md-6 col-xl-3">
                        <div class="widget footer-widget">
                            <div class="th-widget-about">
                                <div class="about-logo">
                                    <a href="../index.html"><img
                                            src="../assets/img/Holistic Health Consultation Center.webp"
                                            alt="Holistic Health Consultation in Dubai"></a>
                                </div>
                                <p class="about-text">Experience Ayurveda's healing at Anjali's Center. Personalized
                                    therapies and holistic guidance await.</p>
                                <div class="working-time">
                                    <span class="title">We Are Available:</span>
                                    <p class="desc">Mon-Sun: 09.00 am to 09.00 pm</p>
                                </div>
                                <div class="th-social  footer-social">
                                    <a target="_blank" href="https://www.facebook.com/DrAnjalisAyurvedicCenter/"><i
                                            class="fab fa-facebook-f"></i></a>
                                    <a target="_blank" href="https://www.instagram.com/dr_anjalis_ayurvedic_center/"><i
                                            class="fab fa-instagram"></i></a>
                                    <a target="_blank" href="https://x.com/AnjaliAyurvedic"><i
                                            class="fa-brands fa-x-twitter"></i></a>
                                    <a target="_blank"
                                        href="https://www.linkedin.com/company/dr-anjali-s-ayurvedic-center"><i
                                            class="fab fa-linkedin-in"></i></a>
                                    <a target="_blank" href="https://www.threads.net/@dr_anjalis_ayurvedic_center"><i
                                            class="fa-brands fa-threads"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <div class="widget widget_nav_menu  footer-widget">
                            <h3 class="widget_title">Quick link</h3>
                            <div class="menu-all-pages-container">
                                <ul class="menu">
                                    <li><a href="../about.html">About Us</a></li>
                                    <li><a href="../doctors.html">Doctors Panel</a></li>
                                    <li><a href="../specialties.html">Our Specialties</a></li>
                                    <li><a href="../service.html">Services</a></li>
                                    <li><a href="../packages.html">Packages</a></li>
                                    <li><a href="../contact.html">Contact Us</a></li>
                                    <li><a href="../appointment-for-ayurveda-doctor.html">Book Appointment</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-auto">
                        <div class="widget footer-widget">
                            <h3 class="widget_title">Contact Details</h3>
                            <div class="th-widget-about">
                                <h4 class="footer-info-title">Phone Number</h4>
                                <p class="footer-info"><i class="fa-sharp fa-solid fa-phone"></i><a class="text-inherit"
                                        href="tel:+97142992881">+97142992881</a></p>
                                <p class="footer-info"><i class="fa-solid fa-mobile-screen"></i><a class="text-inherit"
                                        href="tel:+971524731447">+971524731447</a></p>
                                <h4 class="footer-info-title">Email Address</h4>
                                <p class="footer-info"><i class="fas fa-envelope"></i><a class="text-inherit"
                                        href="mailto:info@dranjalisayurveda.com">info@dranjalisayurveda.com</a></p>
                                <h4 class="footer-info-title">Office Location</h4>
                                <p class="footer-info"><i class="fas fa-map-marker-alt"></i>Wasl Hub, Bldg No R005,<br>
                                    Shop NO S12 & 13 - 1, 39th <br>St - Al Karama - Dubai - United Arab Emirates
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6 col-xl-auto">
        <div class="widget footer-widget">
            <h4 class="widget_title">Newsletter</h4>
            <div class="newsletter-widget">
                <p class="md-10">Sign Up to get updates & news about us . Get Latest Deals from Walker's Inbox to our mail address.</p>
                <div class="footer-search-contact mt-25">
                    <form>
                        <input class="form-control" type="email" placeholder="Enter your email">
                    </form>
                    <div class="footer-btn mt-10">
                        <button type="submit" class="th-btn style3 fw-btn">Subscribe Now <i class="fa-regular fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
                </div>
            </div>
        </div>
        <div class="copyright-wrap">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <p class="copyright-text text-white">© 2024 Dr. Anjali's Ayurvedic Center. All Rights Reserved.
                            <a href="../privacy-policy.html">Privacy Policy</a> |<a href="../privacy-policy.html">Terms
                                of
                                Service</a> | <a href="../sitemap.html">Sitemap</a>
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="footer-links">
                            <ul>
                                <li><a target="_blank" href="https://illforddigital.com/">Designed and Developed by:
                                        Illford Digital</a></li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </footer>



    <style>
        .th-header a {
            text-decoration: none;
        }

        .footer-layout1 a {
            text-decoration: none;
        }

        .th-menu-wrapper a {
            text-decoration: none;
        }
    </style>


    <div class="floating_btn">
        <a target="_blank" href="https://wa.me/+91 1234567890" style="text-decoration: none;">
            <div class="contact_icon">
                <i class="fa fa-whatsapp my-float"></i>
            </div>
        </a>
        <p class="text_icon">Talk to us?</p>
    </div>
    <script src="../js/main.js"></script>
    
    <script src="recentposts.js"></script>
    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5pNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>


        <!--==============================
    All Js File
============================== -->
    <!-- Jquery -->
    <script src="../assets/js/vendor/jquery-3.7.1.min.js"></script>
    <!-- Slick Slider -->
    <script src="../assets/js/slick.min.js"></script>
    <!-- Bootstrap -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <!-- Magnific Popup -->
    <script src="../assets/js/jquery.magnific-popup.min.js"></script>
    <!-- Range Slider -->
    <script src="../assets/js/jquery-ui.min.js"></script>
    <!-- imagesloaded  -->
    <script src="../assets/js/imagesloaded.pkgd.min.js"></script>
    <!-- Isotope Filter -->
    <script src="../assets/js/isotope.pkgd.min.js"></script>
    <!-- flipster Filter -->
    <script src="../assets/js/jquery.flipster.min.js"></script>
    <!-- odometer -->
    <script src="../assets/js/odometer.js"></script>
    <!-- appear -->
    <script src="../assets/js/appear-2.js"></script>
    <!-- Nice Select -->
    <script src="../assets/js/nice-select.min.js"></script>
    <!-- datetimepicker -->
    <script src="../assets/js/jquery.datetimepicker.min.js"></script>

    <!-- tilt -->
    <script src="../assets/js/tilt.min.js"></script>
    <!-- wow -->
    <script src="../assets/js/wow.min.js"></script>
    <!-- Main Js File -->
    <script src="../assets/js/main.js"></script>



    $bodyScripts
    </body>
    </html>
HTML;


    // Save the blog post content to a file in the root directory
    $postFileName = __DIR__ . "/" . $slug . ".html";
    if (file_put_contents($postFileName, $blogPostContent) === false) {
        die("Error: Unable to save the blog post.");
    }

    echo "<script>alert('Post published successfully!'); window.location.href = 'admin.html';</script>";
} else {
    echo "<script>alert('Post saved as private'); window.location.href = 'admin.html';</script>";
}


    // Save the blog post content to a file in the root directory
    $postFileName = __DIR__ . "/" . $slug . ".html";
    if (file_put_contents($postFileName, $blogPostContent) === false) {
        die("Error: Unable to save the blog post.");
    }

    echo "<script>alert('Post published successfully!'); window.location.href = 'admin.html';</script>";
} else {
    echo "<script>alert('Error: Invalid request method.'); window.location.href = 'admin.html';</script>";
}



include_once('clear_temp_json.php');
include_once('clear_temp_json.php');
?>

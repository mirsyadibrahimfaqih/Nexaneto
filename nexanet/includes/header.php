<?php
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../config/session.php';

$page_title = isset($page_title) ? $page_title : 'NexaNet - Portal Digital Terpadu';
$current_page = isset($current_page) ? $current_page : basename($_SERVER['PHP_SELF']);
$canonical = BASE_URL . '/' . $current_page;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(SITE_DESC); ?>">
    <meta name="keywords" content="NexaNet, internet, jaringan, portal digital, status jaringan, tiket gangguan">
    <meta name="author" content="NexaNet">
    <link rel="canonical" href="<?php echo $canonical; ?>">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(SITE_DESC); ?>">
    <meta property="og:url" content="<?php echo $canonical; ?>">
    <meta property="og:site_name" content="NexaNet">
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%230284C7' d='M13 10V3L4 14h7v7l9-11h-7z'/></svg>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {50:'#E0F2FE',100:'#BAE6FD',500:'#0EA5E9',600:'#0284C7',700:'#0369A1',800:'#075985',900:'#0C4A6E'},
                        status: {normal:'#16A34A',maintenance:'#D97706',outage:'#DC2626'},
                        ink: '#111827', surface: '#F8FAFC'
                    },
                    fontFamily: { sans: ['"Plus Jakarta Sans"','Inter','sans-serif'], display: ['"Plus Jakarta Sans"','sans-serif'], body: ['Inter','sans-serif'] },
                    maxWidth: {'container':'1320px'}
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "NexaNet",
        "url": "<?php echo BASE_URL; ?>",
        "description": "<?php echo SITE_DESC; ?>"
    }
    </script>
</head>
<body class="bg-surface text-ink font-body antialiased min-h-screen flex flex-col">
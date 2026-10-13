<?php
ini_set('session.cookie_samesite','Lax'); ini_set('session.cookie_secure','0'); session_start();
if(!isset($_SESSION['user'])){ header('Location: login.php'); exit; }
$isAdmin = $_SESSION['role']=='admin';
$ref = $_GET['ref'] ?? '';
$data = json_decode(file_get_contents('data.json'), true);
$idx=-1; foreach($data as $i=>$c){ if($c['ref']==$ref) {$idx=$i; break;} }
if($idx==-1) die("Not found");
$c=$data[$idx];
if($_SERVER['REQUEST_METHOD']=='POST' && $isAdmin){
 $data[$idx]['status']=$_POST['status'];
 $data[$idx]['admin_response']=$_POST['comment'];
 file_put_contents('data.json', json_encode($data));
 header("Location: complaint_details.php?ref=".$ref); exit;
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=$c['ref']?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
*{box-sizing:border-box;margin:0;padding:0}body{font-family:'Inter',Arial;background:#f5f4ff;display:flex;min-height:100vh}
.sidebar{width:220px;background:#10101e;color:#fff;padding:12px;display:flex;flex-direction:column;position:sticky;top:0;height:100vh}
.top-logo{padding:10px 6px;display:flex;align-items:center;gap:8px;font-weight:800;font-size:13px}
.nav{margin-top:20px}
.nav a{display:flex;gap:10px;padding:12px 14px;border-radius:12px;color:#8b8ba7;text-decoration:none;font-size:13px;margin:6px 0}
.nav a.active{background:#6c5cff;color:#fff;box-shadow:0 4px 20px rgba(108,92,255,.4)}
.main{flex:1;padding:0}
.header{background:#fff;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #eee;position:sticky;top:0}
.header h1{font-size:18px;font-weight:800}
.back{padding:6px 14px;background:#fff;border:1px solid #e5e7eb;border-radius:20px;font-size:12px;text-decoration:none;color:#000}
.content{padding:20px;max-width:900px}
.card{background:#fff;border-radius:16px;padding:20px;border:1px solid #ece9ff;margin-bottom:16px;box-shadow:0 2px 12px rgba(0,0,0,.03)}
.pill{padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block}
.green{background:#eafff0;color:#16a34a;border:1px solid #bbf7d0}.blue{background:#e8f0ff;color:#2563eb}
.small{font-size:11px;color:#6b7280}
.code{background:#f8f9fb;border:1px solid #f1f1f1;border-radius:12px;padding:14px;font-family:monospace;font-size:12px;line-height:1.7;white-space:pre-wrap;margin-top:14px;color:#374151}
.resp{background:#f0fdf4;border:1px solid #c7f0d0;border-radius:12px;padding:12px;margin-top:14px}
label{font-size:11px;font-weight:700;display:block;margin:12px 0 6px}
select,input{width:100%;padding:11px;border:1px solid #e5e7eb;border-radius:10px;font-size:13px}
.btn{background:#6c5cff;color:#fff;border:none;padding:10px 18px;border-radius:10px;font-weight:700;cursor:pointer;margin-top:12px}
</style></head><body>
<div class="sidebar">
<div class="top-logo">📦 ResolveDesk 🔒</div>
<div class="nav">
<a href="dashboard.php">◍ Dashboard</a>
<a class="active" href="#">📄 Complaint Details</a>
</div>
<div style="margin-top:auto;padding:10px;font-size:11px;color:#6b7280"><a href="dashboard.php" style="color:#8b8ba7;text-decoration:none">← Dashboard</a></div>
</div>
<div class="main">
<div class="header"><h1><?=$c['ref']?> - <?=$c['subject']?></h1><a class="back" href="dashboard.php">← Back</a></div>
<div class="content">
<div class="card">
<div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap"><span class="pill green"><?=$c['status']?></span><span class="pill blue"><?=$c['category']?></span><span class="small">ID: <?=$c['id']?> • User: <?=$c['student']?>(2)</span></div>
<h3 style="margin-top:12px;font-size:16px;font-weight:800"><?=$c['subject']?></h3>
<div class="small" style="margin-top:6px">📍 salem • 📅 <?=$c['date']?></div>
<div class="code">cd /home/workspace/my-project/complaint-system

#1. My Complaints - BEAUTIFUL + SECURE + DETAILS
cat &gt; my_complaints.php &lt;&lt; 'PHP';
&lt;?php
session_start();
header(&quot;X-Frame-Options: DENY&quot;);
if(!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])){header(&quot;Location: login.php&quot;); exit; }
if(time() - $_SESSION['login_time'] &gt; 3600){session_destroy(); header(&quot;Location: login.php&quot;); exit; }
$_SESSION['login_time']=time();
$data=json_decode(file_get_contents(__DIR__.'/fake_db.json'),true);</div>
<div class="resp"><div style="font-size:11px;font-weight:700;color:#15803d">💬 Admin Response:</div><div style="font-size:13px;margin-top:4px"><?= htmlspecialchars($c['admin_response']??'ok dear')?></div></div>
</div>
<div class="card">
<div style="font-weight:700;font-size:13px">🔧 Update Status & Response (Admin Only)</div>
<form method="POST">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div><label>New Status</label><select name="status"><option <?= $c['status']=='Pending'?'selected':''?>>Pending</option><option <?= $c['status']=='In Progress'?'selected':''?>>In Progress</option><option <?= $c['status']=='Closed'?'selected':''?>>Closed</option></select></div>
<div><label>Admin Comment</label><input name="comment" value="<?= htmlspecialchars($c['admin_response']??'ok dear')?>"></div>
</div>
<button class="btn">💾 Save Changes</button>
</form>
</div>
</div>
</div>
</body></html>

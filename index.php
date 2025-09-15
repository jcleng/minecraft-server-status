<?php
require_once './vendor/autoload.php';

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
// 服务器状态获取函数
function getMinecraftServerStatus()
{

    $status = [
        'online' => false,
        'error' => null,
        'data' => null
    ];

    try {
        $Query = new MinecraftPing(getenv('SERVER_HOST'), intval(getenv('SERVER_PORT')));
        $data = $Query->Query();
        $status['online'] = true;
        $status['data'] = $data;
    } catch (MinecraftPingException $e) {
        $status['error'] = $e->getMessage();
    } finally {
        if (isset($Query)) {
            $Query->Close();
        }
    }

    return $status;
}

// 获取服务器状态
$serverStatus = getMinecraftServerStatus();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minecraft 服务器状态监控</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4CAF50;
            --primary-dark: #388E3C;
            --secondary: #2196F3;
            --dark: #121212;
            --darker: #0a0a0a;
            --light: #f5f5f5;
            --gray: #333;
            --card-bg: rgba(30, 30, 30, 0.8);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--darker), var(--dark));
            color: var(--light);
            min-height: 100vh;
            padding: 20px;
            background-attachment: fixed;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px;
            position: relative;
        }

        .server-icon-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid rgba(76, 175, 80, 0.3);
            box-shadow: 0 0 30px rgba(76, 175, 80, 0.2);
            overflow: hidden;
        }

        .server-icon {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .default-icon {
            font-size: 60px;
            color: var(--primary);
        }

        h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }

        .subtitle {
            font-size: 1.2rem;
            color: #aaa;
            max-width: 600px;
            margin: 0 auto;
        }

        .status-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: rgba(76, 175, 80, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .card-icon i {
            font-size: 24px;
            color: var(--primary);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .card-content {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }

        .online {
            background: rgba(76, 175, 80, 0.2);
            color: var(--primary);
        }

        .offline {
            background: rgba(244, 67, 54, 0.2);
            color: #f44336;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
            color: var(--primary);
        }

        .stat-label {
            font-size: 1rem;
            color: #aaa;
        }

        .players-container {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .players-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .player-card {
            background: rgba(50, 50, 50, 0.5);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: var(--transition);
        }

        .player-card:hover {
            background: rgba(70, 70, 70, 0.7);
            transform: translateY(-3px);
        }

        .player-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 10px;
            background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .player-name {
            font-weight: 600;
            margin-top: 5px;
        }

        .refresh-btn {
            display: block;
            margin: 30px auto;
            padding: 12px 30px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }

        .refresh-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.6);
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #777;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .status-container {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 2.2rem;
            }

            .server-icon-container {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="server-icon-container">
                <?php if ($serverStatus['online'] && isset($serverStatus['data']['favicon'])) : ?>
                    <img src="<?= $serverStatus['data']['favicon'] ?>" alt="服务器图标" class="server-icon">
                <?php else : ?>
                    <div class="default-icon">
                        <i class="fas fa-server"></i>
                    </div>
                <?php endif; ?>
            </div>
            <h1>Minecraft 服务器状态</h1>
            <p class="subtitle">实时监控服务器状态、玩家信息和性能指标</p>
        </header>

        <div class="status-container">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-plug"></i>
                    </div>
                    <h2 class="card-title">服务器状态</h2>
                </div>
                <div class="card-content">
                    <?php if ($serverStatus['online']) : ?>
                        <p><span class="status-indicator online"><i class="fas fa-check-circle"></i> 在线</span></p>
                        <p>服务器正在运行并接受连接</p>
                    <?php else : ?>
                        <p><span class="status-indicator offline"><i class="fas fa-times-circle"></i> 离线</span></p>
                        <p>服务器当前不可用: <?= htmlspecialchars($serverStatus['error'] ?? '未知错误') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h2 class="card-title">服务器信息</h2>
                </div>
                <div class="card-content">
                    <?php if ($serverStatus['online']) : ?>
                        <p><strong>版本:</strong> <?= htmlspecialchars($serverStatus['data']['version']['name'] ?? '未知') ?></p>
                        <p><strong>协议:</strong> <?= htmlspecialchars($serverStatus['data']['version']['protocol'] ?? '未知') ?></p>
                        <p><strong>描述:</strong> <?= htmlspecialchars($serverStatus['data']['description']['text'] ?? '') ?></p>
                    <?php else : ?>
                        <p>服务器离线，无法获取信息</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2 class="card-title">玩家信息</h2>
                </div>
                <div class="card-content">
                    <?php if ($serverStatus['online']) : ?>
                        <p><strong>在线玩家:</strong> <?= htmlspecialchars($serverStatus['data']['players']['online'] ?? 0) ?> / <?= htmlspecialchars($serverStatus['data']['players']['max'] ?? 0) ?></p>
                        <?php if (isset($serverStatus['data']['players']['sample']) && count($serverStatus['data']['players']['sample']) > 0) : ?>
                            <p><strong>当前玩家:</strong>
                                <?php
                                $playerNames = array_map(function ($player) {
                                    return htmlspecialchars($player['name']);
                                }, $serverStatus['data']['players']['sample']);
                                echo implode(', ', $playerNames);
                                ?>
                            </p>
                        <?php else : ?>
                            <p>当前没有玩家在线</p>
                        <?php endif; ?>
                    <?php else : ?>
                        <p>服务器离线，无法获取玩家信息</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($serverStatus['online']) : ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users fa-2x"></i>
                    <div class="stat-value"><?= htmlspecialchars($serverStatus['data']['players']['online'] ?? 0) ?></div>
                    <div class="stat-label">在线玩家</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-user-plus fa-2x"></i>
                    <div class="stat-value"><?= htmlspecialchars($serverStatus['data']['players']['max'] ?? 0) ?></div>
                    <div class="stat-label">最大玩家数</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-code fa-2x"></i>
                    <div class="stat-value"><?= htmlspecialchars($serverStatus['data']['version']['protocol'] ?? '?') ?></div>
                    <div class="stat-label">协议版本</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-image fa-2x"></i>
                    <div class="stat-value"><?= isset($serverStatus['data']['favicon']) ? '已设置' : '未设置' ?></div>
                    <div class="stat-label">服务器图标</div>
                </div>
            </div>

            <?php if (isset($serverStatus['data']['players']['sample']) && count($serverStatus['data']['players']['sample']) > 0) : ?>
                <div class="players-container">
                    <h2><i class="fas fa-user-friends"></i> 在线玩家</h2>
                    <div class="players-grid">
                        <?php foreach ($serverStatus['data']['players']['sample'] as $player) : ?>
                            <div class="player-card">
                                <div class="player-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="player-name"><?= htmlspecialchars($player['name']) ?></div>
                                <div class="player-id"><?= htmlspecialchars($player['id']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <button class="refresh-btn" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> 刷新状态
        </button>

        <div class="footer">
            <p>Minecraft 服务器状态监控面板 &copy; <?= date('Y') ?></p>
            <p>数据更新时间: <?= date('Y-m-d H:i:s') ?></p>
        </div>
    </div>

    <script>
        // 自动刷新页面（每60秒）
        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
</body>

</html>

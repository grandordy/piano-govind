<!-- includes/menu-toolbar.php -->
<div class="menu-toolbar">
    <button class="menu-btn" onclick="toggleDropdown('keyassist')">KEY ASSIST</button>
    <button class="menu-btn" onclick="toggleDropdown('sound')">SOUND</button>
    <button class="menu-btn" onclick="toggleDropdown('help')">HELP</button>
</div>

<style>
.menu-toolbar {
    background: transparent;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 50px;
    border-radius: 0;
    gap: 2px;
    flex: 1;
}

.menu-btn {
    background: transparent;
    color: #999;
    border: none;
    padding: 15px 25px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.2s ease;
    height: 100%;
    border-radius: 0;
}

.menu-btn:first-child {
    border-radius: 12px 0 0 0;
}

.menu-btn:last-child {
    border-radius: 0 12px 0 0;
}

.menu-btn:hover {
    background: #333;
    color: #fff;
}

.menu-btn.active {
    background: #444;
    color: #fff;
}
</style>
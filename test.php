<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barista Café Menu</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: url('your-background.jpg') no-repeat center center/cover;
      height: 100vh;
      color: white;
    }
    .overlay {
      background: rgba(0, 0, 0, 0.7);
      height: 100%;
      width: 100%;
      padding: 40px;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    header h1 { font-size: 24px; }
    nav a {
      margin: 0 15px;
      color: white;
      text-decoration: none;
      font-weight: bold;
    }
    .menu-container {
      display: flex;
      gap: 40px;
      flex-wrap: wrap;
    }
    .category {
      flex: 1;
      min-width: 300px;
      background: rgba(255, 255, 255, 0.05);
      padding: 20px;
      border-radius: 12px;
    }
    .category h2 {
      font-size: 22px;
      margin-bottom: 20px;
      border-bottom: 2px solid #fff2;
      padding-bottom: 10px;
    }
    .item {
      margin-bottom: 20px;
    }
    .item-title {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .item-title h3 { font-size: 18px; }
    .item-price { font-size: 16px; color: orange; }
    .recommended {
      background: orange;
      color: white;
      padding: 2px 8px;
      font-size: 12px;
      border-radius: 5px;
      margin-left: 10px;
    }
    .desc {
      font-size: 14px;
      color: #ddd;
    }
  </style>
</head>
<body>
  <div class="overlay">
    

    <div class="menu-container">
      <!-- Breakfast Section -->
      <div class="category">
        <h2>🍳 Breakfast</h2>
        <div class="item">
          <div class="item-title">
            <h3>Pancakes</h3>
            <span class="item-price">$12.50</span>
          </div>
          <p class="desc">Fresh brewed coffee and steamed milk</p>
        </div>
        <div class="item">
          <div class="item-title">
            <h3>Toasted Waffle</h3>
            <span class="item-price"><s>$16.50</s> $12.00</span>
          </div>
          <p class="desc">Brewed coffee and steamed milk</p>
        </div>
        <div class="item">
          <div class="item-title">
            <h3>Fried Chips <span class="recommended">Recommend</span></h3>
            <span class="item-price">$15.00</span>
          </div>
          <p class="desc">Rich Milk and Foam</p>
        </div>
      </div>

      <!-- Coffee Section -->
      <div class="category">
        <h2>☕ Coffee</h2>
        <div class="item">
          <div class="item-title">
            <h3>Latte</h3>
            <span class="item-price"><s>$12.50</s> $7.50</span>
          </div>
          <p class="desc">Fresh brewed coffee and steamed milk</p>
        </div>
        <div class="item">
          <div class="item-title">
            <h3>White Coffee <span class="recommended">Recommend</span></h3>
            <span class="item-price">$5.90</span>
          </div>
          <p class="desc">Brewed coffee and steamed milk</p>
        </div>
        <div class="item">
          <div class="item-title">
            <h3>Chocolate Milk</h3>
            <span class="item-price">$5.50</span>
          </div>
          <p class="desc">Rich Milk and Foam</p>
        </div>
      </div>

      <!-- You can copy this block to add more categories -->
      <!-- <div class="category"> ... </div> -->
    </div>
  </div>
</body>
</html>
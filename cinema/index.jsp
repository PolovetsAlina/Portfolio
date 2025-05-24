<!DOCTYPE html>
<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.Connection"%>
<%@ page import="java.sql.DriverManager"%>
<%@ page import="java.sql.PreparedStatement"%>
<%@ page import="java.sql.ResultSet"%>
<%@ page import="java.sql.SQLException"%>
<%@ page import="java.sql.Statement"%>
<%@ page import="java.util.Date"%>
<%@ page import="java.text.SimpleDateFormat"%>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Hub - Prenota il tuo film preferito</title>
    <link rel="stylesheet" href="index.css?v=<%= new java.util.Date().getTime() %>">
</head>
<body>
    <header>
        <nav>
            <div class="logo">CINEMA HUB</div>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="film.jsp">Film</a></li>
                <li><a href="#about">Chi siamo</a></li>
                <li><a href="#contact">Contatti</a></li>
                <% if (session.getAttribute("username") != null) { %>
                    <li><span>Welcome, <%= session.getAttribute("username") %></span></li>
                    <li><a href="logout.jsp" class="logout-btn">Logout</a></li>
                <% } else { %>
                    <li><button onclick="openLoginModal()" class="login-btn">Accedi</button></li>
                <% } %>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Benvenuti al Cinema Hub</h1>
            <p>Scopri i migliori film in programmazione</p>
        </div>
    </section>

    <!-- Sezione Film -->
    <section id="movies" class="movies">
        <h2>Seleziona una data per vedere i film disponibili</h2>
        <input type="date" id="date-picker" class="date-picker" onchange="loadMovies(this.value)" 
               value="<%= request.getParameter("date") != null ? request.getParameter("date") : new SimpleDateFormat("yyyy-MM-dd").format(new Date()) %>">

        <div class="movies-grid">
            <%
                String selectedDate = request.getParameter("date");
                if (selectedDate == null || selectedDate.isEmpty()) {
                    SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                    selectedDate = sdf.format(new Date());
                }

                Connection conn = null;
                PreparedStatement pstmt = null;
                ResultSet rs = null;
                SimpleDateFormat dateFormat = new SimpleDateFormat("dd/MM/yyyy");
                
                try {
                    Class.forName("com.mysql.cj.jdbc.Driver");
                    conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
                    
                    String sql = "SELECT DISTINCT f.Codice_film, f.Titolo, f.immagine, f.Regista, f.Anno, " +
                               "p.Codice_proiezioni, p.Data, p.Ora " +
                               "FROM proiezioni p, film f " +
                               "WHERE p.Codice_film = f.Codice_film " +
                               "AND DATE(p.Data) = ? " +
                               "ORDER BY p.Data, p.Ora";
                    
                    pstmt = conn.prepareStatement(sql);
                    pstmt.setString(1, selectedDate);
                    rs = pstmt.executeQuery();

                    while (rs.next()) {
                        String codiceFilm = rs.getString("Codice_film");
                        String titolo = rs.getString("Titolo");
                        String immagine = "img/" + rs.getString("immagine");
                        String regista = rs.getString("Regista");
                        String anno = rs.getString("Anno");
                        String codiceProiezione = rs.getString("Codice_proiezioni");
                        String data = dateFormat.format(rs.getDate("Data"));
                        String ora = rs.getString("Ora");
            %>
                        <div class="movie-card">
                            <img src="<%= immagine %>" alt="<%= titolo %>">
                            <div class="movie-info">
                                <h3><%= titolo %></h3>
                                <p>Regista: <%= regista %></p>
                                <p>Anno: <%= anno %></p>
                                <p>Data proiezione: <%= data %></p>
                                <p>Ora: <%= ora %></p>
                                <% if (session.getAttribute("username") != null) { %>
                                    <a href="#login" class="book-btn">Prenota</a>
                                <% } else { %>
                                    <button onclick="openLoginModal()" class="book-btn">Login per prenotare</button>
                                <% } %>
                            </div>
                        </div>
            <%
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                } finally {
                    if (rs != null) try { rs.close(); } catch (SQLException e) { }
                    if (pstmt != null) try { pstmt.close(); } catch (SQLException e) { }
                    if (conn != null) try { conn.close(); } catch (SQLException e) { }
                }
            %>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <h2>Accedi</h2>
            <form action="login.jsp" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="submit-btn">Accedi</button>
            </form>
            <div id="toggle-register-modal">
                <p>Non hai un account? <a href="registrazione.jsp">Registrati</a></p>
            </div>
        </div>
    </div>

    <!-- Sezione Login -->
    <section id="login" class="login-section">
        <h2>Accedi</h2>
        <form action="login.jsp" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="submit-btn">Accedi</button>
        </form>

        <div id="toggle-register-section">
            <p>Non hai un account? <a href="registrazione.jsp">Registrati</a></p>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 Cinema Hub. Tutti i diritti riservati.</p>
    </footer>

    <script>
        function openLoginModal() {
            document.getElementById("loginModal").style.display = "block";
        }

        function closeLoginModal() {
            document.getElementById("loginModal").style.display = "none";
        }

        function loadMovies(date) {
            window.location.href = 'index.jsp?date=' + date;
        }
        window.onclick = function(event) {
            var modal = document.getElementById("loginModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>

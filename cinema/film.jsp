<!DOCTYPE html>
<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.*" %>
<%@ page import="java.util.Date" %>
<%@ page import="java.text.SimpleDateFormat" %>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Hub - Film</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .movies {
            padding: 40px 20px;
            background-color: #1a1a1a;
            color: white;
        }
        
        .movies h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 30px;
            font-size: 1.8em;
            font-weight: 600;
        }
        
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px;
        }
        
        .movie-card {
            background: #2d2d2d;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 300px;
            margin: 0 auto;
        }
        
        .movie-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            border-color: rgba(76, 175, 80, 0.3);
        }

        .movie-card img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }
        
        .movie-info {
            padding: 10px 12px;
            text-align: center;
        }
        
        .movie-info h3 {
            margin: 0;
            color: #ffffff;
            font-size: 1.3em;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .movie-info .dettagli {
            display: flex;
            flex-direction: column;
            gap: 3px;
            margin-top: 6px;
        }
        
        .movie-info .dettagli span {
            color: #e0e0e0;
            font-size: 0.95em;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
            margin: 0;
            padding: 0;
        }
        
        .error-message {
            text-align: center;
            color: #f44336;
            padding: 20px;
            margin: 20px;
            background-color: #263238;
            border-radius: 5px;
        }
        
        footer {
            background-color: #1a1a1a;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">CINEMA HUB</div>
            <ul>
                <li><a href="index.jsp">Home</a></li>
                <li><a href="film.jsp">Film</a></li>
                <li><a href="#about">Chi siamo</a></li>
                <li><a href="#contact">Contatti</a></li>
                <% if (session.getAttribute("username") != null) { %>
                    <li><span>Benvenuto, <%= session.getAttribute("username") %></span></li>
                    <li><a href="logout.jsp" class="logout-btn">Esci</a></li>
                <% } else { %>
                    <li><button onclick="openLoginModal()" class="login-btn">Accedi</button></li>
                <% } %>
            </ul>
        </nav>
    </header>

    <section class="movies">
        <h2>Galleria dei Film</h2>
        <div class="movies-grid">
            <%
                Connection conn = null;
                PreparedStatement pstmt = null;
                ResultSet rs = null;
                String errorMessage = null;
                
                try {
                    Class.forName("com.mysql.cj.jdbc.Driver");
                    conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
                    
                    String sql = "SELECT Codice_film, Titolo, immagine, Regista, Anno " +
                               "FROM film ORDER BY Anno DESC";
                    
                    pstmt = conn.prepareStatement(sql);
                    rs = pstmt.executeQuery();

                    boolean hasFilms = false;
                    while (rs.next()) {
                        hasFilms = true;
                        String codiceFilm = rs.getString("Codice_film");
                        String titolo = rs.getString("Titolo");
                        String immagine = "img/" + rs.getString("immagine");
                        String regista = rs.getString("Regista");
                        String anno = rs.getString("Anno");
            %>
            <div class="movie-card">
                <img src="<%= immagine %>" alt="<%= titolo %>">
                <div class="movie-info">
                    <h3><%= titolo %></h3>
                    <div class="dettagli">
                        <span>Regista: <%= regista %></span>
                        <span>Anno: <%= anno %></span>
                    </div>
                </div>
            </div>
            <%
                    }
                    
                    if (!hasFilms) {
                        errorMessage = "Nessun film trovato nel database";
                    }
                } catch (Exception e) {
                    errorMessage = "Errore nel recupero dei film: " + e.getMessage();
                } finally {
                    if (rs != null) try { rs.close(); } catch (SQLException e) { }
                    if (pstmt != null) try { pstmt.close(); } catch (SQLException e) { }
                    if (conn != null) try { conn.close(); } catch (SQLException e) { }
                }

                if (errorMessage != null) {
            %>
            <div class="error-message">
                <%= errorMessage %>
            </div>
            <%
                }
            %>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 Cinema Hub. Tutti i diritti riservati.</p>
    </footer>
</body>
</html>
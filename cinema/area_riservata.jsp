<!DOCTYPE html>
<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.Connection" %>
<%@ page import="java.sql.DriverManager" %>
<%@ page import="java.sql.PreparedStatement" %>
<%@ page import="java.sql.ResultSet" %>
<%@ page import="java.sql.SQLException" %>
<%@ page import="java.sql.Statement" %>
<%@ page import="java.util.*, javax.servlet.http.*" %>
<%
    HttpSession sessione = request.getSession(false);
    if (sessione == null || sessione.getAttribute("username") == null) {
        response.sendRedirect("index.jsp");
        return;
    }
    
    String username = (String) sessione.getAttribute("username");
    String userId = (String) sessione.getAttribute("userId");
    String userType = (String) sessione.getAttribute("userType");
    
    // Redirect admin to area_amministratore.jsp
    if (userType != null && userType.equals("admin")) {
        response.sendRedirect("area_amministratore.jsp");
        return;
    }
%>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Riservata - Cinema Hub</title>
    <link rel="stylesheet" href="index.css?v=<%= new java.util.Date().getTime() %>">
    <script>
        function loadProjections(date) {
            window.location.href = 'area_riservata.jsp?date=' + date;
        }
    </script>
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
                <li><a href="logout.jsp" class="logout-btn">Esci</a></li>
            </ul>
        </nav>
    </header>

    <main class="user-panel">
        <h1>Benvenuto, <%= username %></h1>

        <!-- Navigazione -->
        <nav class="user-nav">
            <a href="#prenotazioni" class="active">Prenotazioni</a>
            <a href="#profilo">Profilo</a>
            <a href="#proiezioni">Proiezioni</a>
        </nav>

        <!-- Prenotazioni Section -->
        <section id="prenotazioni">
            <h2>Le mie prenotazioni</h2>
            <div class="view-all-link">
                <a href="tutte_prenotazioni.jsp" class="admin-btn">Visualizza tutte le prenotazioni</a>
            </div>
            <div class="prenotazioni-list">
                <%
                    Connection connPrenotazioni = null;
                    PreparedStatement pstmtPrenotazioni = null;
                    ResultSet rsPrenotazioni = null;
                    
                    try {
                        Class.forName("com.mysql.cj.jdbc.Driver");
                        connPrenotazioni = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
                        
                        String sql = "SELECT p.Codice_prenotazione, p.Username, p.Codice_proiezione, p.Posto, p.Pagamento, f.Titolo, f.immagine, f.Regista, f.Anno, pr.Data, pr.Ora " +
                                   "FROM prenotazioni p, proiezioni pr, film f " +
                                   "WHERE p.Codice_proiezioni = pr.Codice_proiezioni " +
                                   "AND pr.Codice_film = f.Codice_film " +
                                   "AND p.Username = ? " +
                                   "ORDER BY pr.Data DESC " +
                                   "LIMIT 3";
                        
                        pstmtPrenotazioni = connPrenotazioni.prepareStatement(sql);
                        pstmtPrenotazioni.setString(1, username);
                        rsPrenotazioni = pstmtPrenotazioni.executeQuery();
                        
                        while(rsPrenotazioni.next()) {
                %>
                            <div class="prenotazione-card">
                                <img src="img/<%= rsPrenotazioni.getString("immagine") %>" alt="<%= rsPrenotazioni.getString("Titolo") %>">
                                <div class="prenotazione-info">
                                    <h3><%= rsPrenotazioni.getString("Titolo") %></h3>
                                    <p>Regista: <%= rsPrenotazioni.getString("Regista") %></p>
                                    <p>Anno: <%= rsPrenotazioni.getString("Anno") %></p>
                                    <p>Data: <%= rsPrenotazioni.getString("Data") %></p>
                                    <p>Ora: <%= rsPrenotazioni.getString("Ora") %></p>
                                    <p>Posto: <%= rsPrenotazioni.getString("Posto") %></p>
                                    <div class="payment-status">
                                        <span>Stato del pagamento:</span>
                                        <div class="status-badge <%= rsPrenotazioni.getString("Pagamento").equals("si") ? "status-active" : "status-inactive" %>">
                                            <%= rsPrenotazioni.getString("Pagamento") %>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <%
                        }
                    } catch(Exception e) {
                        out.println("<p>Errore nel caricamento delle prenotazioni: " + e.getMessage() + "</p>");
                    } finally {
                        try { if (rsPrenotazioni != null) rsPrenotazioni.close(); } catch (Exception e) { }
                        try { if (pstmtPrenotazioni != null) pstmtPrenotazioni.close(); } catch (Exception e) { }
                        try { if (connPrenotazioni != null) connPrenotazioni.close(); } catch (Exception e) { }
                    }
                %>
            </div>
        </section>

        <!-- Profilo Section -->
        <section id="profilo">
            <h2>Il mio profilo</h2>
            <div class="admin-form">
                <form action="updateProfile.jsp" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<%= username %>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Lascia vuoto per non modificare">
                    </div>
                    <button type="submit" class="admin-btn">Aggiorna Profilo</button>
                </form>
            </div>
        </section>

        <!-- Proiezioni Section -->
        <section id="proiezioni">
            <h2>Proiezioni</h2>
            
            <!-- Date Picker -->
            <div class="date-filter">
                <label for="dateSelect">Seleziona una data:</label>
                <input type="date" id="dateSelect" name="dateSelect" onchange="loadProjections(this.value)" 
                       style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #ff4444; padding: 0.5rem; border-radius: 5px; 
                              -webkit-text-fill-color: white; -webkit-opacity: 1; opacity: 1;">
            </div>

            <div class="proiezioni-container">
                <style>
                    .proiezioni-container {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                        gap: 2rem;
                        padding: 2rem;
                        max-width: 1400px;
                        margin: 0 auto;
                    }

                    .proiezione-card {
                        background: rgba(255, 255, 255, 0.05);
                        border-radius: 15px;
                        overflow: hidden;
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        border: 1px solid rgba(255, 255, 255, 0.1);
                        backdrop-filter: blur(10px);
                        display: flex;
                        flex-direction: column;
                    }

                    .proiezione-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
                    }

                    .proiezione-image {
                        position: relative;
                        overflow: hidden;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: rgba(0, 0, 0, 0.1);
                        flex: 1;
                    }

                    .proiezione-image img {
                        position: static;
                        width: 100%;
                        height: auto;
                        object-fit: contain;
                        transition: transform 0.3s ease;
                    }

                    .proiezione-info {
                        padding: 1rem;
                        background: rgba(0, 0, 0, 0.2);
                        flex: 0 0 auto;
                    }

                    .proiezione-info h3 {
                        color: #ff4444;
                        margin: 0 0 1rem 0;
                        font-size: 1.25rem;
                        text-align: center;
                    }

                    .proiezione-info p {
                        color: #ffffff;
                        margin: 0.5rem 0;
                        font-size: 0.95rem;
                        text-align: center;
                    }

                    .proiezione-info .prenota-btn {
                        display: inline-block;
                        padding: 0.8rem 1.5rem;
                        background: #ff4444;
                        color: white;
                        text-decoration: none;
                        border-radius: 25px;
                        font-weight: bold;
                        transition: transform 0.3s ease, background 0.3s ease;
                        margin-top: 1rem;
                    }

                    .proiezione-info .prenota-btn:hover {
                        transform: translateY(-2px);
                        background: #ff2d2d;
                    }

                    .proiezione-info .prenota-btn:active {
                        transform: translateY(0);
                    }

                    .no-proiezioni {
                        text-align: center;
                        padding: 2rem;
                        color: #ff4444;
                        font-size: 1.25rem;
                    }
                </style>
<%
    String selectedDate = request.getParameter("date");
    try {
        Class.forName("com.mysql.jdbc.Driver");
        Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        String query = "SELECT p.*, f.Titolo, f.immagine " +
                      "FROM proiezioni p " +
                      "JOIN film f ON p.Codice_film = f.Codice_film " +
                      "WHERE 1=1 " +
                      "ORDER BY p.Data DESC, p.Ora DESC";
        
        if (selectedDate != null && !selectedDate.isEmpty()) {
            query = "SELECT p.*, f.Titolo, f.immagine " +
                   "FROM proiezioni p " +
                   "JOIN film f ON p.Codice_film = f.Codice_film " +
                   "WHERE DATE(p.Data) = ? " +
                   "ORDER BY p.Data DESC, p.Ora DESC";
        }
        
        PreparedStatement pstmt = conn.prepareStatement(query);
        if (selectedDate != null && !selectedDate.isEmpty()) {
            pstmt.setString(1, selectedDate);
        }
        
        ResultSet rs = pstmt.executeQuery();
        
        while (rs.next()) {
            String codiceProiezione = rs.getString("Codice_proiezioni");
            String titolo = rs.getString("Titolo");
            String data = rs.getString("Data");
            String ora = rs.getString("Ora");
            String immagine = rs.getString("immagine");
%>
                            <div class="proiezione-card">
                                <div class="proiezione-image">
                                    <img src="img/<%= immagine %>?v=<%= new java.util.Date().getTime() %>" alt="<%= titolo %>">
                                </div>
                                <div class="proiezione-info">
                                    <h3><%= titolo %></h3>
                                    <p>Data: <%= data %></p>
                                    <p>Ora: <%= ora %></p>
                                    <p>Sala: 6</p>
                                    <%
                                    // Get current date
                                    java.util.Date currentDate = new java.util.Date();
                                    java.sql.Date sqlCurrentDate = new java.sql.Date(currentDate.getTime());
                                    
                                    // Compare dates
                                    java.sql.Date projectionDate = java.sql.Date.valueOf(data);
                                    boolean isPastDate = projectionDate.before(sqlCurrentDate);
                                    
                                    if (isPastDate) { %>
                                        <p class="text-muted">Proiezione terminata</p>
                                    <% } else { %>
                                        <a href="prenota.jsp?proiezione_id=<%= codiceProiezione %>&sala=6" class="prenota-btn">Prenota</a>
                                    <% } %>
                                </div>
                            </div>
<%
        }
        
        rs.close();
        pstmt.close();
        conn.close();
    } catch (Exception e) {
        out.println("<div class='no-proiezioni'>Errore nel caricamento delle proiezioni: " + e.getMessage() + "</div>");
    }
%>
            </div>
        </section>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('dateSelect');
            
            dateInput.setAttribute('placeholder', 'gg/mm/aaaa');
            
            dateInput.addEventListener('change', function() {
                const date = new Date(this.value);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();

                this.value = `${day}/${month}/${year}`;
            });
        });
    </script>

    <style>
        .date-filter {
            margin: 2rem 0;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
    </style>

    <footer>
        <p>&copy; 2025 Cinema Hub. Tutti i diritti riservati.</p>
    </footer>
</body>
</html>

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
%>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutte le mie prenotazioni - Cinema Hub</title>
    <link rel="stylesheet" href="index.css?v=<%= new java.util.Date().getTime() %>">
</head>
<body>
    <header>
        <nav>
            <div class="logo">CINEMA HUB</div>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#movies">Film</a></li>
                <li><a href="#about">Chi siamo</a></li>
                <li><a href="#contact">Contatti</a></li>
                <li><a href="logout.jsp" class="logout-btn">Esci</a></li>
            </ul>
        </nav>
    </header>

    <main class="user-panel">
        <h1>Benvenuto, <%= username %></h1>
        
        <section id="prenotazioni">
            <div class="back-link">
                <a href="area_riservata.jsp" class="admin-btn">Torna indietro</a>
            </div>
            
            <h2>Tutte le mie prenotazioni</h2>
            <div class="prenotazioni-list">
                <%
                    Connection conn = null;
                    PreparedStatement pstmt = null;
                    ResultSet rs = null;
                    
                    try {
                        Class.forName("com.mysql.cj.jdbc.Driver");
                        conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
                        
                        String sql = "SELECT p.Codice_prenotazione, p.Username, p.Codice_proiezione, p.Posto, p.Pagamento, f.Titolo, f.immagine, f.Regista, f.Anno, pr.Data, pr.Ora " +
                                   "FROM prenotazioni p, proiezioni pr, film f " +
                                   "WHERE p.Codice_proiezione = pr.Codice_proiezioni " +
                                   "AND pr.Codice_film = f.Codice_film " +
                                   "AND p.Username = ? " +
                                   "ORDER BY pr.Data DESC";
                        
                        pstmt = conn.prepareStatement(sql);
                        pstmt.setString(1, username);
                        rs = pstmt.executeQuery();
                        
                        while(rs.next()) {
                %>
                            <div class="prenotazione-card">
                                <img src="img/<%= rs.getString("immagine") %>" alt="<%= rs.getString("Titolo") %>">
                                <div class="prenotazione-info">
                                    <h3><%= rs.getString("Titolo") %></h3>
                                    <p>Regista: <%= rs.getString("Regista") %></p>
                                    <p>Anno: <%= rs.getString("Anno") %></p>
                                    <p>Data: <%= rs.getString("Data") %></p>
                                    <p>Ora: <%= rs.getString("Ora") %></p>
                                    <p>Posto: <%= rs.getString("Posto") %></p>
                                    <div class="payment-status">
                                        <span>Stato del pagamento:</span>
                                        <div class="status-badge <%= rs.getString("Pagamento").equals("si") ? "status-active" : "status-inactive" %>">
                                            <%= rs.getString("Pagamento") %>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <%
                        }
                    } catch(Exception e) {
                        out.println("<p>Errore nel caricamento delle prenotazioni: " + e.getMessage() + "</p>");
                    } finally {
                        try { if (rs != null) rs.close(); } catch (Exception e) { }
                        try { if (pstmt != null) pstmt.close(); } catch (Exception e) { }
                        try { if (conn != null) conn.close(); } catch (Exception e) { }
                    }
                %>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Cinema Hub. Tutti i diritti riservati.</p>
    </footer>
</body>
</html>

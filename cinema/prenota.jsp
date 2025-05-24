<!DOCTYPE html>
<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.*" %>
<html>
<head>
    <meta charset="UTF-8">
    <title>Prenota - Cinema</title>
    <style>
        body {
            background-color: #1a1a1a;
            color: white;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 600px;
            width: 90%;
            background-color: #2a2a2a;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
        .movie-details {
            background-color: #3a3a3a;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .movie-details h3 {
            color: #ff4444;
            margin: 0 0 15px 0;
            font-size: 24px;
        }
        
        .movie-details p {
            color: #ffffff;
            margin: 5px 0;
            font-size: 18px;
        }
        
        form {
            background-color: #3a3a3a;
            padding: 25px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .seat-label {
            color: #ff4444;
            margin: 15px 0;
            display: block;
            font-size: 16px;
            text-align: center;
        }
        
        select {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            background-color: #444;
            color: white;
            border: 1px solid #555;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        select:hover {
            background-color: #555;
        }
        
        .prenota-btn {
            width: 100%;
            padding: 15px;
            background-color: #ff4444;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .prenota-btn:hover {
            background-color: #cc0000;
            transform: translateY(-2px);
        }
        
        .error {
            color: #ff4444;
            margin: 15px 0;
            text-align: center;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            background-color: rgba(255, 68, 68, 0.1);
        }
        
        .success {
            color: #4CAF50;
            margin: 15px 0;
            text-align: center;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            background-color: rgba(76, 175, 80, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
<%
    // Check if user is logged in
    if (session.getAttribute("username") == null) {
        response.sendRedirect("index.jsp");
        return;
    }

    String username = (String)session.getAttribute("username");
    String proiezioneId = request.getParameter("proiezione_id");
    String postoParam = request.getParameter("posto");
    String message = "";
    
    if (proiezioneId != null && postoParam != null) {
        Connection conn = null;
        PreparedStatement pstmt = null;
        ResultSet rs = null;
        
        try {
            Class.forName("com.mysql.jdbc.Driver");
            conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
            
            // Ultimo Codice_prenotazione
            String getLastCodeQuery = "SELECT Codice_prenotazione FROM prenotazioni ORDER BY Codice_prenotazione DESC LIMIT 1";
            pstmt = conn.prepareStatement(getLastCodeQuery);
            rs = pstmt.executeQuery();
            
            String nextCode;
            if (rs.next()) {
                String lastCode = rs.getString("Codice_prenotazione");
                // Autoincremento
                int number = Integer.parseInt(lastCode.substring(2)) + 1;
                nextCode = "AA" + String.format("%04d", number);
            } else {
                // Start from AA0001 
                nextCode = "AA0001";
            }
            
            String checkQuery = "SELECT COUNT(*) FROM prenotazioni WHERE Codice_proiezione = ? AND Posto = ?";
            pstmt = conn.prepareStatement(checkQuery);
            pstmt.setString(1, proiezioneId);
            pstmt.setString(2, postoParam);
            rs = pstmt.executeQuery();
            rs.next();
            
            if (rs.getInt(1) > 0) {
                message = "Posto gi&agrave; occupato. Seleziona un altro posto.";
            } else {
                // Get data from proiezioni table
                String selectQuery = "SELECT Data FROM proiezioni WHERE Codice_proiezioni = ?";
                pstmt = conn.prepareStatement(selectQuery);
                pstmt.setString(1, proiezioneId);
                rs = pstmt.executeQuery();
                
                if (rs.next()) {
                    java.sql.Date dataPresentazione = rs.getDate("Data");
                    
                    // Insert into prenotazioni table with sala 6
                    String insertQuery = "INSERT INTO prenotazioni (Codice_prenotazione, Username, Codice_proiezione, Data_presentazione, Sala, Posto, Pagamento) VALUES (?, ?, ?, ?, 6, ?, 'si')";
                    pstmt = conn.prepareStatement(insertQuery);
                    pstmt.setString(1, nextCode);
                    pstmt.setString(2, username);
                    pstmt.setString(3, proiezioneId);
                    pstmt.setDate(4, dataPresentazione);
                    pstmt.setString(5, postoParam);
                    
                    int rowsAffected = pstmt.executeUpdate();
                    
                    if (rowsAffected > 0) {
                        message = "Prenotazione effettuata con successo! Posto: " + postoParam + " in sala 6";
                        response.sendRedirect("area_riservata.jsp");
                        return;
                    } else {
                        message = "Errore durante l&apos;inserimento della prenotazione.";
                    }
                } else {
                    message = "Proiezione non trovata.";
                }
            }
        } catch (Exception e) {
            message = "Errore durante la prenotazione: " + e.getMessage();
        } finally {
            if (rs != null) try { rs.close(); } catch (SQLException e) {}
            if (pstmt != null) try { pstmt.close(); } catch (SQLException e) {}
            if (conn != null) try { conn.close(); } catch (SQLException e) {}
        }
    }

    // Display form
    try {
        Class.forName("com.mysql.jdbc.Driver");
        Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        String query = "SELECT p.Data, f.Titolo, pr.Posto " +
                      "FROM proiezioni p " +
                      "LEFT JOIN film f ON p.Codice_film = f.Codice_film " +
                      "LEFT JOIN prenotazioni pr ON p.Codice_proiezioni = pr.Codice_proiezioni " +
                      "WHERE p.Codice_proiezioni = ?";
        
        PreparedStatement pstmt = conn.prepareStatement(query);
        pstmt.setString(1, proiezioneId);
        ResultSet rs = pstmt.executeQuery();
        
        if (rs.next()) {
%>
            <div class="movie-details">
                <h3><%= rs.getString("Titolo") %></h3>
                <p>Data: <%= rs.getDate("Data") %></p>
                <p>Sala: 6</p>
            </div>
            
            <% if (!message.isEmpty()) { %>
                <div class="<%= message.contains("successo") ? "success" : "error" %>"><%= message %></div>
            <% } %>
            
            <form action="prenota.jsp" method="post">
                <input type="hidden" name="proiezione_id" value="<%= proiezioneId %>">
                <div class="seat-label">Seleziona il posto</div>
                <select name="posto" required>
                    <option value="">Seleziona il posto</option>
                    <%
                        java.util.Set<String> occupiedSeats = new java.util.HashSet<>();
                        do {
                            String occupiedSeat = rs.getString("Posto");
                            if (occupiedSeat != null) {
                                occupiedSeats.add(occupiedSeat);
                            }
                        } while (rs.next());
                        
                        for (int num = 1; num <= 20; num++) {
                            for (char letter = 'A'; letter <= 'I'; letter++) {
                                String seat = String.format("%02d%c", num, letter);
                                if (!occupiedSeats.contains(seat)) {
                    %>
                                    <option value="<%= seat %>"><%= seat %></option>
                    <%
                                }
                            }
                        }
                    %>
                </select>
                <button type="submit" class="prenota-btn">Prenota</button>
            </form>
<%
        } else {
%>
            <div class="error">Proiezione non trovata.</div>
<%
        }
        rs.close();
        pstmt.close();
        conn.close();
    } catch (Exception e) {
%>
        <div class="error">Errore: <%= e.getMessage() %></div>
<%
    }
%>
    </div>
</body>
</html>